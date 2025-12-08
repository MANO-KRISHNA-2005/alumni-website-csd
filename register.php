<?php
// Start the session to enable security features like CSRF tokens
session_start();

// Include the security helper functions
require_once 'security_helpers.php';

// Generate a CSRF token
 $csrf_token = generate_csrf_token();

// Get any stored form data from previous submission
 $stored_data = get_stored_form_data();

// --- ERROR HANDLING ---
// Check if the URL has an error parameter and show it in a popup
 $error_message = '';
 $success_message = '';

if (isset($_GET['error'])) {
    $error_code = $_GET['error'];
    switch ($error_code) {
        case 'missing_fields':
            $error_message = 'Error: Please fill in all required fields.';
            break;
        case 'invalid_email':
            $error_message = 'Error: Please enter a valid email address.';
            break;
        case 'invalid_gender':
            $error_message = 'Error: Please select a valid gender option.';
            break;
        case 'invalid_year':
            $error_message = 'Error: Please select a valid graduation year.';
            break;
        case 'invalid_phone':
            $error_message = 'Error: Please enter a valid phone number.';
            break;
        case 'invalid_expertise':
            $error_message = 'Error: Please select your years of expertise.';
            break;
        case 'duplicate_email':
            $error_message = 'Error: This email address has already been registered.';
            break;
        case 'db_error':
            $error_message = 'Error: A database error occurred. Please try again later.';
            break;
        case 'csrf_error':
            $error_message = 'Security error: Invalid request. Please refresh the page and try again.';
            break;
        case 'invalid_request':
            $error_message = 'Error: Invalid request method.';
            break;
        case 'captcha_error':
            $error_message = 'Error: Incorrect captcha. Please try again.';
            break;
        default:
            $error_message = 'An unknown error occurred. Please try again.';
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $success_message = 'Registration submitted successfully!';
}

// Helper function to get value from stored data or default
function get_form_value($field, $default = '') {
    global $stored_data;
    return $stored_data[$field] ?? $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>B.Sc. CSD Alumni Meet | REBOOT 40</title>
  <!-- Your existing CSS and font links remain the same -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Russo+One&family=Bebas+Neue&family=Righteous&family=Monoton&family=Iceberg&family=Changa+One&family=Press+Start+2P&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- International Telephone Input CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">
  <style>
    /* --- ALL YOUR EXISTING CSS GOES HERE --- */
    :root {
      --neon-yellow: #FFD700;
      --neon-yellow-glow: rgba(255, 215, 0, 0.4);
      --gold-yellow: #FFC107;
      --gold-yellow-glow: rgba(255, 193, 7, 0.4);
      --amber-yellow: #FFB300;
      --amber-yellow-glow: rgba(255, 179, 0, 0.4);
      --honey-yellow: #FFEB3B;
      --honey-yellow-glow: rgba(255, 235, 59, 0.4);
      --mustard-yellow: #FFC400;
      --mustard-yellow-glow: rgba(255, 196, 0, 0.4);
      --neon-white: #FFFFFF;
      --neon-white-glow: rgba(255, 255, 255, 0.4);
      --ivory-white: #FFFFF0;
      --ivory-white-glow: rgba(255, 255, 240, 0.4);
      --cream-white: #FFFACD;
      --cream-white-glow: rgba(255, 250, 205, 0.4);
      --pearl-white: #F8F8FF;
      --pearl-white-glow: rgba(248, 248, 255, 0.4);
      --snow-white: #FFFAFA;
      --snow-white-glow: rgba(255, 250, 250, 0.4);
      --crimson-red: #DC143C;
      --crimson-red-glow: rgba(220, 20, 60, 0.4);
      --fire-red: #B22222;
      --fire-red-glow: rgba(178, 34, 34, 0.4);
      --dark-bg: #000000;
      --darker-bg: #050505;
      --charcoal-bg: #121212;
      --grid-color: rgba(255, 215, 0, 0.1);
      --transition: all 0.3s ease;
      --rotate-speed: 40;
      --count: 6;
      --easeInOutSine: cubic-bezier(0.37, 0, 0.63, 1);
      --easing: cubic-bezier(0.000, 0.37, 1.000, 0.63);
      
      /* Form specific variables */
      --bg-color: #2a2a15;
      --text-color: #ffcc00;
      --blue-accent: #00a8ff;
      --red-accent: #ff0055;
      --purple-accent: #9c27b0;
      --input-bg: rgba(42, 42, 21, 0.4);
      --input-border: #3a506b;
      --card-bg: rgba(42, 42, 21, 0.15);
      --error-color: #ff3366;
      --success-color: #4CAF50;
      --button-bg: #ffcc00;
      --button-text: #000000;
      --focus-color: #00a8ff;
      --hover-bg: rgba(42, 42, 21, 0.6);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Remove default browser focus outlines and selection colors */
    *:focus {
      outline: none;
    }

    ::selection {
      background-color: rgba(255, 204, 0, 0.3);
      color: #ffffff;
    }

    ::-moz-selection {
      background-color: rgba(255, 204, 0, 0.3);
      color: #ffffff;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background: var(--bg-color);
      color: var(--text-color);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
      padding-top: 100px; /* Added to account for fixed navbar */
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(0, 168, 255, 0.1) 0%, transparent 40%),
        radial-gradient(circle at 90% 80%, rgba(255, 0, 85, 0.1) 0%, transparent 40%);
      position: relative;
      overflow-x: hidden;
    }

    /* ===== NAVIGATION BAR ===== */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(15px);
  border-bottom: 2px solid var(--neon-yellow);
  z-index: 1000;
  box-shadow: 0 0 10px var(--neon-yellow-glow);
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
      filter: drop-shadow(0 0 4px var(--neon-yellow-glow));
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
    }

    .nav-link:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--neon-yellow);
      transition: var(--transition);
    }

    .nav-link:hover,
    .nav-link.active {
      color: var(--neon-yellow);
      text-shadow: 0 0 4px var(--neon-yellow-glow);
    }

    .nav-link:hover:after,
    .nav-link.active:after {
      width: 100%;
      box-shadow: 0 0 4px var(--neon-yellow-glow);
    }

    .nav-link.btn {
      background: var(--neon-yellow);
      color: var(--dark-bg);
      padding: 6px 12px;
      border-radius: 4px;
      box-shadow: 0 0 8px var(--neon-yellow-glow);
    }

    .nav-link.btn:hover {
      background: #ffdd33;
    }

    .hamburger {
      display: none;
      cursor: pointer;
      color: var(--neon-yellow);
      font-size: 1.2rem;
      z-index: 1001;
      text-shadow: 0 0 4px var(--neon-yellow-glow);
    }

    /* Background Video Container */
    .video-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    #bgVideo {
      position: absolute;
      top: 50%;
      left: 50%;
      min-width: 100%;
      min-height: 100%;
      width: auto;
      height: auto;
      transform: translateX(-50%) translateY(-50%);
      object-fit: cover;
      opacity: 0.35;
    }

    /* Fallback background image in case video doesn't load */
    .video-fallback {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #1a1a0d, #2a2a15);
      z-index: -2;
    }

    /* Frame Border Effects for Container */
    .container {
      width: 100%;
      max-width: 1200px;
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.15);
      transition: transform 0.3s ease;
      position: relative;
      z-index: 1;
    }

    .container::before {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background-size: 400% 400%;
      border-radius: 17px;
      z-index: -1;
      animation: borderGlow 8s ease infinite;
      opacity: 0.7;
      filter: blur(5px);
    }

    .container::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: inherit;
      border-radius: 15px;
      z-index: -1;
    }

    @keyframes borderGlow {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    .container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }

    .header {
      padding: 30px;
      background: linear-gradient(135deg, rgba(42, 42, 21, 0.3), rgba(60, 60, 30, 0.3));
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
    }

    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 0;
    }

    .header-content {
      position: relative;
      z-index: 1;
    }

    .header h1 {
      font-family: 'Audiowide', cursive; /* 3D stylish font */
      font-size: 2.8rem;
      margin-bottom: 10px;
      color: var(--text-color);
      text-transform: uppercase;
      letter-spacing: 3px;
      /* Enhanced 3D elevated effect with deeper shadows */
      text-shadow:
        1px 1px 2px #000,
        2px 2px 4px #000,
        3px 3px 6px rgba(0, 0, 0, 0.9),
        4px 4px 8px rgba(0, 0, 0, 0.8),
        6px 6px 12px rgba(0, 0, 0, 0.6),
        8px 8px 16px rgba(0, 0, 0, 0.4),
        0 0 20px #FFD700,
        0 0 40px #FFC107,
        0 0 60px rgba(255, 215, 0, 0.5);
      transform: perspective(500px) rotateX(5deg);
      transform-style: preserve-3d;
      position: relative;
      display: inline-block;
    }

    .header h1::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 10%;
      width: 80%;
      height: 3px;
      border-radius: 3px;
    }

    .header p {
      font-family: 'Russo One', sans-serif; /* 3D stylish font */
      color: #fff;
      font-size: 1.2rem;
      margin-top: 15px;
      font-weight: 600;
      /* Enhanced 3D elevated effect */
      text-shadow:
        1px 1px 2px #000,
        2px 2px 4px #000,
        3px 3px 6px rgba(0, 0, 0, 0.9),
        4px 4px 8px rgba(0, 0, 0, 0.8),
        0 0 8px #fff,
        0 0 15px rgba(255,255,255,0.7),
        0 0 25px rgba(255,255,255,0.5);
      transform: perspective(500px) rotateX(3deg);
      transform-style: preserve-3d;
    }

    .form-container {
      padding: 30px;
      background: rgba(42, 42, 21, 0.2);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
    }

    .form-title {
      font-family: 'Bebas Neue', cursive; /* 3D stylish font */
      text-align: center;
      background: linear-gradient(135deg, #333, #555);
      color: white;
      padding: 12px 0;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 1.5rem;
      font-weight: 600;
      letter-spacing: 1px;
      /* Enhanced 3D elevated effect */
      text-shadow:
        1px 1px 2px #000,
        2px 2px 4px #000,
        3px 3px 6px rgba(0, 0, 0, 0.9),
        4px 4px 8px rgba(0, 0, 0, 0.8),
        0 0 8px rgba(255,255,255,0.7),
        0 0 15px rgba(255,255,255,0.5);
      transform: perspective(500px) rotateX(2deg);
      transform-style: preserve-3d;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }

    .col-33 {
      flex: 1 1 30%;
      min-width: 280px;
    }

    .col-50 {
      flex: 1 1 48%;
      min-width: 280px;
      box-sizing: border-box;
    }

    .col-66 {
      flex: 1 1 65%;
      min-width: 280px;
    }

    .col-100 {
      flex: 1 1 100%;
      min-width: 280px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      flex: 1;
      position: relative;
    }

    /* Individual field frame effects */
    .form-group::after {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      border-radius: 10px;
      background-size: 300% 300%;
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }

    .form-group:focus-within::after {
      opacity: 0.4;
      animation: fieldGlow 3s ease infinite;
    }

    @keyframes fieldGlow {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    form label {
      font-weight: 500;
      font-size: 0.95rem;
      color: #f0f0f0;
      display: block;
      margin-bottom: 8px;
      letter-spacing: 0.5px;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .required {
      color: #ff3366;
      margin-left: 3px;
    }

    form input, form select, form textarea {
      width: 100%;
      padding: 14px;
      border: 1px solid var(--input-border);
      border-radius: 8px;
      background: var(--input-bg);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      color: var(--text-color);
      font-size: 1rem;
      outline: none;
      transition: all 0.3s ease;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    @keyframes borderPulse {
      0% {
        box-shadow: 0 0 0 2px rgba(0, 168, 255, 0.3), 0 0 20px rgba(0, 168, 255, 0.2);
      }
      50% {
        box-shadow: 0 0 0 4px rgba(0, 168, 255, 0.1), 0 0 30px rgba(0, 168, 255, 0.3);
      }
      100% {
        box-shadow: 0 0 0 2px rgba(0, 168, 255, 0.3), 0 0 20px rgba(0, 168, 255, 0.2);
      }
    }

    /* Custom select styling to avoid default browser colors */
    form select {
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffcc00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 20px;
      padding-right: 40px;
    }

    /* Remove default checkbox and radio styling */
    input[type="checkbox"], input[type="radio"] {
      accent-color: var(--blue-accent);
      width: 18px;
      height: 18px;
      margin-right: 5px;
    }

    .full-width {
      grid-column: span 2;
    }

    form textarea {
      resize: vertical;
      min-height: 100px;
    }

    .section-title {
      font-family: 'Righteous', cursive; /* 3D stylish font */
      font-size: 1.3rem;
      font-weight: 700;
      margin: 25px 0 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.25);
      color: var(--blue-accent);
      text-transform: uppercase;
      letter-spacing: 1px;
      text-align: center;
      /* Enhanced 3D elevated effect */
      text-shadow:
        1px 1px 2px #000,
        2px 2px 4px #000,
        3px 3px 6px rgba(0, 0, 0, 0.9),
        4px 4px 8px rgba(0, 0, 0, 0.8),
        0 0 10px #fff,
        0 0 20px rgba(255,255,255,0.6);
      transform: perspective(500px) rotateX(2deg);
      transform-style: preserve-3d;
    }

    .btn {
      padding: 14px 24px;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-align: center;
      letter-spacing: 0.5px;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      position: relative;
      overflow: hidden;
    }

    /* Enhanced Ghost Animation for Submit Button */
    .btn-primary {
      background: linear-gradient(135deg, 
        var(--button-bg) 0%,
        #ffdd33 50%,
        var(--button-bg) 100%);
      color: var(--button-text);
      box-shadow: 
        0 4px 15px rgba(255, 204, 0, 0.4),
        0 0 20px rgba(255, 215, 0, 0.3);
      position: relative;
      overflow: hidden;
      transition: all 0.4s ease;
      background-size: 200% 200%;
      animation: gradientShift 3s ease infinite;
      border: 2px solid transparent;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.3), 
        transparent);
      transition: left 0.7s ease;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary::after {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background: linear-gradient(45deg, 
        #FFD700, #FFC107, #FFB300, #FFEB3B, #FFD700);
      background-size: 400% 400%;
      border-radius: 10px;
      z-index: -1;
      opacity: 0;
      animation: ghostGlow 2s linear infinite;
      transition: opacity 0.3s ease;
    }

    .btn-primary:hover::after {
      opacity: 1;
    }

    .btn-primary:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 
        0 8px 25px rgba(255, 204, 0, 0.6),
        0 0 30px rgba(255, 215, 0, 0.5);
      animation: pulse 1.5s infinite;
    }

    .btn-primary:active {
      transform: translateY(-2px) scale(0.98);
    }

    @keyframes gradientShift {
      0%, 100% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
    }

    @keyframes ghostGlow {
      0% {
        background-position: 0% 50%;
        filter: brightness(1);
      }
      25% {
        background-position: 100% 50%;
        filter: brightness(1.2);
      }
      50% {
        background-position: 0% 50%;
        filter: brightness(1.5);
      }
      75% {
        background-position: 100% 50%;
        filter: brightness(1.2);
      }
      100% {
        background-position: 0% 50%;
        filter: brightness(1);
      }
    }

    @keyframes pulse {
      0% {
        box-shadow: 
          0 8px 25px rgba(255, 204, 0, 0.6),
          0 0 30px rgba(255, 215, 0, 0.5);
      }
      50% {
        box-shadow: 
          0 8px 30px rgba(255, 204, 0, 0.8),
          0 0 40px rgba(255, 215, 0, 0.7);
      }
      100% {
        box-shadow: 
          0 8px 25px rgba(255, 204, 0, 0.6),
          0 0 30px rgba(255, 215, 0, 0.5);
      }
    }

    .button-group {
      display: flex;
      gap: 15px;
      margin-top: 20px;
      justify-content: center;
    }

    .hidden {
      display: none;
    }

    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      margin: 25px 0;
    }

    .icon {
      font-size: 1.2rem;
    }

    /* Error message styling */
    .error-message {
      color: var(--error-color);
      font-size: 0.85rem;
      margin-top: 5px;
      display: none;
    }

    .input-error {
      border-color: var(--error-color) !important;
      box-shadow: 0 0 0 2px rgba(255, 51, 102, 0.2) !important;
    }

    /* Custom Radio Button with Tick Symbol */
    .radio-group {
      display: flex;
      gap: 15px;
      align-items: center;
      margin-bottom: 10px;
    }

    .radio-group label {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 0;
      cursor: pointer;
      position: relative;
      padding-left: 5px;
    }

    /* Hide the default radio button */
    .radio-group input[type="radio"] {
      position: absolute;
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* Create custom radio button */
    .radio-group .radio-custom {
      position: relative;
      display: inline-block;
      width: 22px;
      height: 22px;
      border: 2px solid var(--input-border);
      border-radius: 50%;
      background: var(--input-bg);
      transition: all 0.3s ease;
      flex-shrink: 0;
    }

    /* Tick symbol */
    .radio-group .radio-custom::after {
      content: '';
      position: absolute;
      display: none;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 8px;
      height: 8px;
      background: var(--blue-accent);
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    /* Alternative tick symbol using checkmark */
    .radio-group .radio-custom.checkmark::after {
      content: '✓';
      width: auto;
      height: auto;
      color: var(--blue-accent);
      font-size: 14px;
      font-weight: bold;
      background: transparent;
    }

    /* When radio button is checked */
    .radio-group input[type="radio"]:checked + .radio-custom {
      border-color: var(--blue-accent);
      background: rgba(0, 168, 255, 0.1);
      box-shadow: 0 0 8px rgba(0, 168, 255, 0.3);
    }

    .radio-group input[type="radio"]:checked + .radio-custom::after {
      display: block;
    }

    /* Hover effect */
    .radio-group label:hover .radio-custom {
      border-color: var(--blue-accent);
      background: rgba(0, 168, 255, 0.05);
    }

    /* Focus effect */
    .radio-group input[type="radio"]:focus + .radio-custom {
      box-shadow: 0 0 0 3px rgba(0, 168, 255, 0.3);
    }

    .small-question {
      margin-top: 10px;
      margin-bottom: 5px;
      font-weight: 600;
    }

    .note {
      font-size: 0.8rem;
      color: #aaa;
      margin-top: 5px;
    }

    /* Logo styling */
    .logo {
      height: 60px;
      margin-bottom: 15px;
    }

    /* Checkbox styling for "Same as Mobile Number" */
    .checkbox-group {
      display: flex;
      align-items: center;
      margin-top: 10px;
      margin-bottom: 15px;
    }

    .checkbox-group input[type="checkbox"] {
      width: 20px;
      height: 20px;
      margin-right: 10px;
      cursor: pointer;
    }

    .checkbox-group label {
      margin-bottom: 0;
      cursor: pointer;
    }

    /* Phone input group with international format - FIXED FOR RESPONSIVE DESIGN */
    .phone-input-group {
      position: relative;
      width: 100%;
    }
    
    .iti {
      width: 100%;
    }

    /* Captcha styling */
    .captch_box {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .captch_box input {
      flex: 1;
      background: var(--input-bg);
      border: 1px solid var(--input-border);
      color: var(--text-color);
      font-size: 22px;
      font-weight: bold;
      letter-spacing: 3px;
      padding: 10px 15px;
      border-radius: 8px;
      pointer-events: none;
    }

    .refresh_button {
      position: relative;
      background: var(--blue-accent);
      height: 40px;
      width: 40px;
      border: none;
      border-radius: 4px;
      color: #fff;
      cursor: pointer;
      margin-left: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .refresh_button:active {
      transform: scale(0.98);
    }

    .captch_input input {
      background: var(--input-bg);
      border: 1px solid var(--input-border);
      color: var(--text-color);
      font-size: 1rem;
      padding: 14px;
      border-radius: 8px;
      width: 100%;
    }

    /* Notification styling */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      z-index: 10000;
      max-width: 300px;
      font-family: 'Roboto', sans-serif;
      font-size: 14px;
      animation: slideIn 0.3s ease-out;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .notification.error {
      background: var(--error-color);
      color: white;
    }

    .notification.success {
      background: var(--success-color);
      color: white;
    }

    .notification-close {
      background: none;
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
      margin-left: 10px;
    }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    /* Success popup styling */
    .success-popup {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 10000;
      backdrop-filter: blur(5px);
    }

    .success-popup-content {
      background: var(--card-bg);
      padding: 40px;
      border-radius: 15px;
      text-align: center;
      max-width: 500px;
      width: 90%;
      border: 2px solid var(--success-color);
      box-shadow: 0 0 20px rgba(76, 175, 80, 0.5);
    }

    .success-popup h2 {
      color: var(--success-color);
      margin-bottom: 20px;
      font-size: 2rem;
    }

    .success-popup p {
      margin-bottom: 30px;
      font-size: 1.1rem;
    }

    .countdown {
      font-size: 1.2rem;
      color: var(--text-color);
      margin-top: 20px;
    }

    /* Mobile layout */
    @media (max-width: 768px) {
      .container {
        width: 95%;
      }
      
      .row {
        flex-direction: column;
      }
      
      .col-33, .col-50, .col-66 {
        flex: 1 1 100%;
      }
      
      .button-group {
        flex-direction: column;
      }
      
      .header h1 {
        font-size: 2.2rem;
      }
      
      .hamburger {
        display: block;
      }
      
      .nav-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 280px;
        height: 100vh;
        background: rgba(5, 5, 16, 0.98);
        backdrop-filter: blur(12px);
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        padding-top: 70px;
        transition: var(--transition);
        z-index: 999;
        border-left: 2px solid rgba(255, 215, 0, 0.4);
        gap: 0;
      }

      .nav-menu.active {
        right: 0;
      }

      .nav-item {
        margin: 0;
        width: 100%;
      }

      .nav-link {
        display: block;
        padding: 10px 18px;
        width: 100%;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo">
        <img src="assets/images/logo.png" alt="PSG Tech Logo" class="logo-img">
      </div>
<ul class="nav-menu">
    <li class="nav-item"><a href="javascript:void(0);" onclick="goToIndexSection('home')" class="nav-link">Home</a></li>
    <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
    <li class="nav-item"><a href="javascript:void(0);" onclick="goToIndexSection('reboot-explanation')" class="nav-link">REBOOT 40</a></li>
    <li class="nav-item"><a href="memories.php" class="nav-link ">Memories</a></li>
    <li class="nav-item"><a href="Gallery.html" class="nav-link">Gallery</a></li>
    <li class="nav-item"><a href="javascript:void(0);" onclick="goToIndexSection('schedule')" class="nav-link">Schedule</a></li>
    <li class="nav-item"><a href="javascript:void(0);" onclick="goToIndexSection('contact')" class="nav-link">Contact</a></li>
    <li class="nav-item"><a href="register.php" class="nav-link btn active">Register</a></li>
</ul>
      <div class="hamburger">
        <i class="fas fa-bars"></i>
      </div>
    </div>
  </nav>

  <!-- Background Video Container -->
  <div class="video-background">
    <video autoplay muted loop playsinline id="bgVideo">
      <source src="assets/videos/bg.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>
  
  <!-- Fallback background -->
  <div class="video-fallback"></div>

  <!-- Success Popup -->
  <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="success-popup" id="successPopup">
    <div class="success-popup-content">
      <h2>Success!</h2>
      <p>Your registration has been submitted successfully!</p>
      <p>You will be redirected to the homepage in <span id="countdown">5</span> seconds.</p>
      <div class="button-group">
        <a href="index.html" class="btn btn-primary">Go to Homepage Now</a>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Main Registration Page -->
  <div class="container" id="registration-page">
    <div class="header">
      <div class="header-content">
        <h1>ALUMNI MEET</h1>
        <p>B.Sc. Computer Systems and Design</p>
      </div>
    </div>

    <div class="form-container">
      <h3 class="form-title">Alumni Registration</h3>
      <form id="alumniForm" action="register_process.php" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token; ?>">
        <!-- CAPTCHA FIX: Add a hidden field to store the generated CAPTCHA text -->
        <input type="hidden" name="captcha_text" id="captcha_text" value="">
        
        <!-- 1st Row: Name, Gender, DOB -->
        <div class="row">
          <div class="col-33">
            <div class="form-group">
              <label for="name">Full Name <span class="required">*</span></label>
              <input type="text" id="name" name="name" required placeholder="e.g. John Doe K" value="<?php echo htmlspecialchars(get_form_value('name')); ?>">
              <div class="error-message" id="name-error">Please enter your full name</div>
            </div>
          </div>
          <div class="col-33">
            <div class="form-group">
              <label>Gender <span class="required">*</span></label>
              <div class="radio-group">
                <label>
                  <input type="radio" name="gender" id="gender-male" value="Male" required <?php echo get_form_value('gender') === 'Male' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Male
                </label>
                <label>
                  <input type="radio" name="gender" id="gender-female" value="Female" required <?php echo get_form_value('gender') === 'Female' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Female
                </label>
              </div>
              <div class="error-message" id="gender-error">Please select your gender</div>
            </div>
          </div>

          <div class="col-33">
            <div class="form-group">
              <label for="email">Email ID <span class="required">*</span></label>
              <input type="email" id="email" name="email" required placeholder="Enter the email-id" value="<?php echo htmlspecialchars(get_form_value('email')); ?>">
              <div class="error-message" id="email-error">Please enter a valid email address</div>
            </div>
          </div>
        </div>

        <!-- 2nd Row: Email ID, Year of Graduation -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="dob">Date of Birth <span class="required">*</span></label>
              <input type="date" id="dob" name="dob" required value="<?php echo htmlspecialchars(get_form_value('dob')); ?>">
              <div class="error-message" id="dob-error">Please enter your date of birth</div>
            </div>
          </div>
          <div class="col-50">
            <div class="form-group">
              <label for="pyear">Year of Graduation <span class="required">*</span></label>
              <select id="pyear" name="pyear" required>
                <option value="">-- Select --</option>
                <?php
                $selected_year = get_form_value('pyear');
                for ($year = 1985; $year <= 2025; $year++) {
                  echo "<option value='$year'" . ($selected_year == $year ? ' selected' : '') . ">$year</option>";
                }
                ?>
              </select>
              <div class="error-message" id="pyear-error">Please select your graduation year</div>
            </div>
          </div>
        </div>

        <!-- 3rd Row: Contact Number, WhatsApp Number -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="phone">Contact Number <span class="required">*</span></label>
              <div class="phone-input-group">
                <input type="tel" id="phone" name="phone" required placeholder="+91 9876543210" value="<?php echo htmlspecialchars(get_form_value('phone')); ?>">
              </div>
              <div class="error-message" id="phone-error">Please enter a valid phone number</div>
            </div>
          </div>
          <div class="col-50">
            <div class="form-group">
              <label for="whatsapp">WhatsApp Number <span class="required">*</span></label>
              <div class="phone-input-group">
                <input type="tel" id="whatsapp" name="whatsapp" required placeholder="+91 9876543210" value="<?php echo htmlspecialchars(get_form_value('whatsapp')); ?>">
              </div>
              <div class="error-message" id="whatsapp-error">Please enter a valid WhatsApp number</div>
              
              <!-- Checkbox to copy contact number to WhatsApp -->
              <div class="checkbox-group">
                <input type="checkbox" id="sameAsPhone" name="sameAsPhone" <?php echo get_form_value('sameAsPhone') ? 'checked' : ''; ?>>
                <label for="sameAsPhone">Same as Contact Number</label>
              </div>
            </div>
          </div>
        </div>

        <!-- 4th Row: Years of Expertise, Domain of Expertise -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="expertise">Years of Expertise <span class="required">*</span></label>
              <select id="expertise" name="expertise" required>
                <option value="">-- Select --</option>
                <option value="0-1" <?php echo get_form_value('expertise') === '0-1' ? 'selected' : ''; ?>>0-1 years</option>
                <option value="1-3" <?php echo get_form_value('expertise') === '1-3' ? 'selected' : ''; ?>>1-3 years</option>
                <option value="3-5" <?php echo get_form_value('expertise') === '3-5' ? 'selected' : ''; ?>>3-5 years</option>
                <option value="5-10" <?php echo get_form_value('expertise') === '5-10' ? 'selected' : ''; ?>>5-10 years</option>
                <option value="10+" <?php echo get_form_value('expertise') === '10+' ? 'selected' : ''; ?>>10+ years</option>
              </select>
              <div class="error-message" id="expertise-error">Please select your years of expertise</div>
            </div>
          </div>
          <div class="col-50">
            <div class="form-group">
              <label for="domain">Domain of Expertise <span class="required">*</span></label>
              <input type="text" id="domain" name="domain" required placeholder="e.g. Web Development, Data Science" value="<?php echo htmlspecialchars(get_form_value('domain')); ?>">
              <div class="error-message" id="domain-error">Please enter your domain of expertise</div>
            </div>
          </div>
        </div>

        <!-- 5th Row: Current Designation, Company Address -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="currentPosition">Current Designation <span class="required">*</span></label>
              <input type="text" id="currentPosition" name="currentPosition" placeholder="e.g. Software Engineer" required value="<?php echo htmlspecialchars(get_form_value('currentPosition')); ?>">
              <div class="error-message" id="currentPosition-error">Please enter your current position</div>
            </div>
          </div>

          <div class="col-50">
            <div class="form-group">
              <label for="currentAddress">Permanent Address <span class="required">*</span></label>
              <input type="text" id="currentAddress" name="currentAddress" placeholder="Enter your permanent address" required value="<?php echo htmlspecialchars(get_form_value('currentAddress')); ?>">
              <div class="error-message" id="currentAddress-error">Please enter your permanent address</div>
            </div>
          </div>
        </div>

        <!-- 6th Row: Permanent Address, LinkedIn -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="currentCompany">Company Address <span class="required">*</span></label>
              <input type="text" id="currentCompany" name="currentCompany" placeholder="e.g. TCS" required value="<?php echo htmlspecialchars(get_form_value('currentCompany')); ?>">
              <div class="error-message" id="currentCompany-error">Please enter your company address</div>
            </div>
          </div>
          <div class="col-50">
            <div class="form-group">
              <label for="linkedin">LinkedIn ID</label>
              <input type="text" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/username" value="<?php echo htmlspecialchars(get_form_value('linkedin')); ?>">
              <div class="error-message" id="linkedin-error">Please enter a valid LinkedIn URL</div>
            </div>
          </div>
        </div>

        <!-- 7th Row: Participating with Family -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="family">Participating with Family Members <span class="required">*</span></label>
              <div class="radio-group">
                <label>
                  <input type="radio" name="family" id="family-yes" value="Yes" required <?php echo get_form_value('family') === 'Yes' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Yes
                </label>
                <label>
                  <input type="radio" name="family" id="family-no" value="No" required <?php echo get_form_value('family') === 'No' || !get_form_value('family') ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  No
                </label>
              </div>
              <div class="error-message" id="family-error">Please select an option</div>
            </div>
          </div>
          <div class="col-50">
            <div class="form-group" id="familyCountInline" style="display: <?php echo get_form_value('family') === 'Yes' ? 'flex' : 'none'; ?>;">
              <div class="family-count-inline">
                <label for="familyCount">How many people are joining? <span class="required">*</span></label>
                <input type="number" id="familyCount" name="familyCount" min="1" placeholder="Enter number" value="<?php echo htmlspecialchars(get_form_value('familyCount')); ?>">
              </div>
              <div class="error-message" id="familyCount-error">Please enter a valid number</div>
            </div>
          </div>
        </div>

        <!-- 8th Row: Nostalgic Memory about Faculty -->
        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label for="nostalgicMemory">Share a nostalgic memory about faculty members you'd like to meet again</label>
              <textarea id="nostalgicMemory" name="nostalgicMemory" placeholder="Share your memories about teachers or subjects that left a lasting impression..." rows="4"><?php echo htmlspecialchars(get_form_value('nostalgicMemory')); ?></textarea>
              <div class="error-message" id="nostalgicMemory-error">Please enter valid text</div>
            </div>
          </div>
        </div>
        
        <div class="row">
        </div>
        <!-- Event Questions - Each in separate rows -->
        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label>Are you interested in PERFORMING in the cultural events?<span class="required">*</span></label>
              <div class="radio-group">
                <label>
                  <input type="radio" name="cultural" id="cultural-yes" value="Yes" <?php echo get_form_value('cultural') === 'Yes' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Yes
                </label>
                <label>
                  <input type="radio" name="cultural" id="cultural-no" value="No" <?php echo get_form_value('cultural') === 'No' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  No
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label>Are you interested in PARTICIPATING in the Gaming events?<span class="required">*</span></label>
              <div class="radio-group">
                <label>
                  <input type="radio" name="gaming" id="gaming-yes" value="Yes" <?php echo get_form_value('gaming') === 'Yes' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Yes
                </label>
                <label>
                  <input type="radio" name="gaming" id="gaming-no" value="No" <?php echo get_form_value('gaming') === 'No' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  No
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label>Are you interested in PARTICIPATING in the Tech Talk events?<span class="required">*</span></label>
              <div class="radio-group">
                <label>
                  <input type="radio" name="techmusic" id="techmusic-yes" value="Yes" <?php echo get_form_value('techmusic') === 'Yes' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  Yes
                </label>
                <label>
                  <input type="radio" name="techmusic" id="techmusic-no" value="No" <?php echo get_form_value('techmusic') === 'No' ? 'checked' : ''; ?>>
                  <span class="radio-custom checkmark"></span>
                  No
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- CAPTCHA Section -->
        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label for="captcha">CAPTCHA Verification <span class="required">*</span></label>
              <div class="input_field captch_box">
                <input type="text" id="captcha-display" value="" disabled />
                <button type="button" class="refresh_button" id="refresh-captcha">
                  <i class="fa-solid fa-rotate-right"></i>
                </button>
              </div>
              <div class="input_field captch_input">
                <input type="text" id="captcha-input" name="captcha" placeholder="Enter captcha" />
              </div>
              <div class="error-message" id="captcha-error">Please enter correct captcha</div>
            </div>
          </div>
        </div>

        <div class="button-group">
          <button type="submit" class="btn btn-primary" style="flex: 1; padding: 16px;">
            <span class="icon">✅</span>
            Submit Registration
          </button>
        </div>
      </form>
    </div>
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
      // Show error or success notifications
      <?php if ($error_message): ?>
        showNotification('<?php echo addslashes($error_message); ?>', 'error');
      <?php endif; ?>
      
      <?php if ($success_message): ?>
        showNotification('<?php echo addslashes($success_message); ?>', 'success');
      <?php endif; ?>
      
      // Auto-redirect after successful registration
      <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const countdownInterval = setInterval(function() {
          countdown--;
          countdownElement.textContent = countdown;
          
          if (countdown <= 0) {
            clearInterval(countdownInterval);
            window.location.href = 'index.html';
          }
        }, 1000);
      <?php endif; ?>
      
      // Function to show notifications
      function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
          <span>${message}</span>
          <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Add close functionality
        notification.querySelector('.notification-close').addEventListener('click', function() {
          notification.remove();
        });
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
          if (notification.parentElement) {
            notification.remove();
          }
        }, 5000);
      }
      
      // Handle video playback
      const video = document.getElementById('bgVideo');
      
      // Try to play video
      video.play().catch(function(error) {
        console.log("Video autoplay failed:", error);
        // If autoplay fails, we can add a play button or handle it differently
      });
      
      // Handle video errors
      video.addEventListener('error', function() {
        console.log("Video loading error");
        // Video failed to load, but we have a fallback background
      });
      
      // Mobile menu toggle
      const hamburger = document.querySelector('.hamburger');
      const navMenu = document.querySelector('.nav-menu');
      
      hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
      });
      
      // Close mobile menu when clicking on a link
      document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
      }));
      
      // Show/hide family count field based on family selection
      const familyYes = document.getElementById('family-yes');
      const familyNo = document.getElementById('family-no');
      const familyCountInline = document.getElementById('familyCountInline');
      
      function toggleFamilyCount() {
        if (familyYes.checked) {
          familyCountInline.style.display = 'flex';
          document.getElementById('familyCount').setAttribute('required', 'required');
        } else {
          familyCountInline.style.display = 'none';
          document.getElementById('familyCount').removeAttribute('required');
        }
      }
      
      familyYes.addEventListener('change', toggleFamilyCount);
      familyNo.addEventListener('change', toggleFamilyCount);
      
      // Handle "Same as Contact Number" checkbox
      const sameAsPhoneCheckbox = document.getElementById('sameAsPhone');
      const phoneInput = document.getElementById('phone');
      const whatsappInput = document.getElementById('whatsapp');

      sameAsPhoneCheckbox.addEventListener('change', function() {
        if (this.checked) {
          // If checked, copy contact number to WhatsApp and disable the field
          whatsappInput.value = phoneInput.value;
          whatsappInput.setAttribute('disabled', 'disabled');
          whatsappInput.style.backgroundColor = '#555'; // Visual cue that it's disabled
        } else {
          // If unchecked, enable the field and clear its value
          whatsappInput.removeAttribute('disabled');
          whatsappInput.style.backgroundColor = ''; // Reset to original style
          whatsappInput.value = ''; // Clear the value
        }
      });

      // Also, update WhatsApp number if contact number changes while the checkbox is checked
      phoneInput.addEventListener('input', function() {
        if (sameAsPhoneCheckbox.checked) {
          whatsappInput.value = phoneInput.value;
        }
      });
      
      // Initialize international telephone input
      const phoneInputField = document.getElementById('phone');
      const whatsappInputField = document.getElementById('whatsapp');
      
      const phoneIti = window.intlTelInput(phoneInputField, {
        initialCountry: "in",
        separateDialCode: false,
        nationalMode: false,
        autoPlaceholder: "aggressive",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
      });
      
      const whatsappIti = window.intlTelInput(whatsappInputField, {
        initialCountry: "in",
        separateDialCode: false,
        nationalMode: false,
        autoPlaceholder: "aggressive",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
      });
      
      // Function to normalize phone number format
      function normalizePhone(iti, input) {
        let val = input.value.replace(/\s+/g, ""); // remove all spaces temporarily
        
        // Extract '+' if exists
        if (!val.startsWith("+")) val = "+" + val;
        
        // Extract possible dial part
        const match = val.match(/^\+(\d{1,4})/);
        let dial = iti.getSelectedCountryData().dialCode;
        
        if (match) {
          dial = match[1]; // user typed code
        }
        
        // digits after that
        let rest = val.replace("+" + dial, "");
        rest = rest.replace(/\D/g, ""); // only digits
        
        input.value = `+${dial}` + (rest ? " " + rest : " ");
      }
      
      // When typing — auto fix spacing
      phoneInputField.addEventListener('input', function() {
        normalizePhone(phoneIti, phoneInputField);
        if (sameAsPhoneCheckbox.checked) {
          // Update WhatsApp field with country code and number
          whatsappInputField.value = phoneInputField.value;
          // Also update the country selection for WhatsApp
          whatsappIti.setCountry(phoneIti.getSelectedCountryData().iso2);
        }
      });
      
      whatsappInputField.addEventListener('input', function() {
        normalizePhone(whatsappIti, whatsappInputField);
      });
      
      // When selecting a country — auto update prefix but keep numbers typed
      phoneInputField.addEventListener('countrychange', function() {
        normalizePhone(phoneIti, phoneInputField);
        if (sameAsPhoneCheckbox.checked) {
          // Update WhatsApp field with country code and number
          whatsappInputField.value = phoneInputField.value;
          // Also update the country selection for WhatsApp
          whatsappIti.setCountry(phoneIti.getSelectedCountryData().iso2);
        }
      });
      
      whatsappInputField.addEventListener('countrychange', function() {
        normalizePhone(whatsappIti, whatsappInputField);
      });
      
// CAPTCHA FUNCTIONALITY (FIXED)
const captchaTextBox = document.querySelector("#captcha-display");
const refreshButton = document.querySelector("#refresh-captcha");
const captchaInputBox = document.querySelector("#captcha-input");
const captchaErrorMessage = document.querySelector("#captcha-error");

// Variable to store generated captcha
let captchaText = null;

// Function to generate captcha
const generateCaptcha = () => {
  // Randomly choose character set: 0=uppercase, 1=lowercase, 2=numbers
  const charSetType = Math.floor(Math.random() * 3);
  let chars = '';
  
  if (charSetType === 0) {
    // Uppercase letters only
    chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  } else if (charSetType === 1) {
    // Lowercase letters only
    chars = 'abcdefghijklmnopqrstuvwxyz';
  } else {
    // Numbers only
    chars = '0123456789';
  }
  
  let captcha = '';
  for (let i = 0; i < 6; i++) {
    captcha += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  
  captchaText = captcha;
  captchaTextBox.value = captchaText;
  
  // CAPTCHA FIX: Store the captcha in the hidden field
  document.getElementById('captcha_text').value = captchaText;
  
  // Debug: Log to console for testing
  console.log("Generated CAPTCHA:", captchaText);
};

const refreshBtnClick = () => {
  generateCaptcha();
  captchaInputBox.value = "";
  captchaInputBox.classList.remove("input-error");
  captchaErrorMessage.style.display = "none";
};

// Add event listeners for the refresh button
refreshButton.addEventListener("click", refreshBtnClick);

// Generate a captcha when the page loads
generateCaptcha();

      // Validation functions
      function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
      }
      
      function validateYear(year) {
        const yearNum = parseInt(year);
        const currentYear = new Date().getFullYear();
        return !isNaN(yearNum) && yearNum >= 1985 && yearNum <= 2025;
      }
      
      function validateLinkedIn(url) {
        // Basic validation for LinkedIn URL
        const linkedinRegex = /^(https?:\/\/)?(www\.)?linkedin\.com\/.+/i;
        return url === '' || linkedinRegex.test(url);
      }

      function showError(inputId, errorId) {
        document.getElementById(inputId).classList.add('input-error');
        document.getElementById(errorId).style.display = 'block';
      }

      function hideError(inputId, errorId) {
        document.getElementById(inputId).classList.remove('input-error');
        document.getElementById(errorId).style.display = 'none';
      }

      // Real-time validation
      document.getElementById('email').addEventListener('blur', function() {
        if (!validateEmail(this.value)) {
          showError('email', 'email-error');
        } else {
          hideError('email', 'email-error');
        }
      });

      document.getElementById('pyear').addEventListener('blur', function() {
        if (!validateYear(this.value)) {
          showError('pyear', 'pyear-error');
        } else {
          hideError('pyear', 'pyear-error');
        }
      });

      document.getElementById('linkedin').addEventListener('blur', function() {
        if (!validateLinkedIn(this.value)) {
          showError('linkedin', 'linkedin-error');
        } else {
          hideError('linkedin', 'linkedin-error');
        }
      });

      // Form submission
      document.getElementById('alumniForm').addEventListener('submit', function(e) {
        let isValid = true;
        let firstErrorField = null;
        
        // --- VALIDATION LOGIC ---
        // Name validation
        if (document.getElementById('name').value.trim() === '') {
          showError('name', 'name-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'name';
        }
        
        // Gender validation
        const genderSelected = document.querySelector('input[name="gender"]:checked');
        if (!genderSelected) {
          document.getElementById('gender-error').style.display = 'block';
          isValid = false;
          if (!firstErrorField) firstErrorField = 'gender-male';
        } else {
          document.getElementById('gender-error').style.display = 'none';
        }
        
        // DOB validation
        if (document.getElementById('dob').value === '') {
          showError('dob', 'dob-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'dob';
        }
        
        // Email validation
        if (!validateEmail(document.getElementById('email').value)) {
          showError('email', 'email-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'email';
        }
        
        // Year validation
        if (!validateYear(document.getElementById('pyear').value)) {
          showError('pyear', 'pyear-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'pyear';
        }
        
        // Expertise validation
        if (document.getElementById('expertise').value === '') {
          showError('expertise', 'expertise-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'expertise';
        }
        
        // Domain validation
        if (document.getElementById('domain').value.trim() === '') {
          showError('domain', 'domain-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'domain';
        }
        
        // LinkedIn validation (optional but if provided must be valid)
        if (document.getElementById('linkedin').value !== '' && !validateLinkedIn(document.getElementById('linkedin').value)) {
          showError('linkedin', 'linkedin-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'linkedin';
        }
        
        // Current position validation
        if (document.getElementById('currentPosition').value.trim() === '') {
          showError('currentPosition', 'currentPosition-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'currentPosition';
        }
        
        // Current company validation
        if (document.getElementById('currentCompany').value.trim() === '') {
          showError('currentCompany', 'currentCompany-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'currentCompany';
        }
        
        // Current address validation
        if (document.getElementById('currentAddress').value.trim() === '') {
          showError('currentAddress', 'currentAddress-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'currentAddress';
        }
        
        // Family validation
        const familySelected = document.querySelector('input[name="family"]:checked');
        if (!familySelected) {
          document.getElementById('family-error').style.display = 'block';
          isValid = false;
          if (!firstErrorField) firstErrorField = 'family-yes';
        } else {
          document.getElementById('family-error').style.display = 'none';
          
          // Family count validation (only if family is "Yes")
          if (familySelected.value === 'Yes') {
            const familyCount = document.getElementById('familyCount').value;
            if (familyCount === '' || parseInt(familyCount) < 1) {
              showError('familyCount', 'familyCount-error');
              isValid = false;
              if (!firstErrorField) firstErrorField = 'familyCount';
            }
          }
        }
        
        // If validation fails, prevent the form from submitting
        if (!isValid) {
          e.preventDefault(); // Stop the form submission
          // Scroll to the first error
          if (firstErrorField) {
            document.getElementById(firstErrorField).scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }
        // If validation passes, do nothing. The form will automatically submit
        // to the 'action' specified in the form tag (register_process.php).
      });
    });
  </script>
  <script>
// Universal function to navigate to any section in index.html without preloader
function goToIndexSection(sectionId) {
    // Set flags to control behavior in index.html
    sessionStorage.setItem('skipPreloader', 'true');
    sessionStorage.setItem('targetSection', sectionId);
    
    // Navigate to index.html
    window.location.href = 'index.html';
}
</script>
</body>
</html>