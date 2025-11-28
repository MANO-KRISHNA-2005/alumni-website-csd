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
            $error_message = 'Error: Please enter a valid phone number (include country code if outside India).';
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
  <title>B.Sc. CSD Alumni Meet</title>
  <!-- Your existing CSS and font links remain the same -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Russo+One&family=Bebas+Neue&family=Righteous&family=Monoton&family=Iceberg&family=Changa+One&family=Press+Start+2P&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      padding: 12px 0;
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
      padding: 8px 0;
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

    .container {
      width: 850px;
      background: var(--card-bg);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.15);
      transition: transform 0.3s ease;
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
      background: 
        radial-gradient(circle at 20% 30%, rgba(0, 168, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(255, 0, 85, 0.15) 0%, transparent 50%);
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
      background: linear-gradient(90deg, transparent, var(--text-color), transparent);
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
    }

    .col-50 {
      flex: 1 1 48%;
      box-sizing: border-box;
    }

    .col-66 {
      flex: 1 1 65%;
    }

    .col-100 {
      flex: 1 1 100%;
    }
    

    .form-group {
      display: flex;
      flex-direction: column;
      flex: 1;
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

    /* Custom select styling to avoid default browser colors */
    form select {
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffcc00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 20px;
      padding-right: 40px;
    }

    form input:focus, form select:focus, form textarea:focus {
      border-color: var(--focus-color);
      box-shadow: 0 0 0 2px rgba(0, 168, 255, 0.3);
      background: var(--hover-bg);
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
    }

    .btn-primary {
      background: var(--button-bg);
      color: var(--button-text);
      box-shadow: 0 4px 15px rgba(255, 204, 0, 0.4);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255, 204, 0, 0.5);
      background: #ffdd33;
    }

    .btn:active {
      transform: translateY(-1px);
    }

    .btn:focus {
      box-shadow: 0 0 0 3px rgba(0, 168, 255, 0.5);
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

    /* Phone input group */
    .phone-input-group {
      display: flex;
      gap: 10px;
    }

    .phone-input-group .country-code {
      flex: 0 0 120px;
    }

    .phone-input-group .phone-number {
      flex: 1;
    }

    /* Family count inline styling */
    .family-count-inline {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }

    .family-count-inline label {
      margin-bottom: 0;
      white-space: nowrap;
    }

    .family-count-inline input {
      width: 80px;
    }

    /* Desktop layout */
    @media (min-width: 768px) {
      form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
      }
      
      .full-width {
        grid-column: span 2;
      }
      
      .button-group {
        grid-column: span 2;
      }
    }

    /* Mobile layout */
    @media (max-width: 768px) {
      .container {
        width: 95%;
      }
      
      .row {
        flex-direction: column;
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

      .phone-input-group {
        flex-direction: column;
      }

      .phone-input-group .country-code {
        flex: 1;
      }

      .family-count-inline {
        flex-direction: column;
        align-items: flex-start;
      }
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
            <!-- UPDATED LINKS -->
            <li class="nav-item"><a href="index.html#home" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
            <li class="nav-item"><a href="index.html#reboot-explanation" class="nav-link">REBOOT 40</a></li>
            <li class="nav-item"><a href="memories.php" class="nav-link">Memories</a></li>
            <li class="nav-item"><a href="galleryhtml" class="nav-link">Gallery</a></li>
            <li class="nav-item"><a href="index.html#schedule" class="nav-link">Schedule</a></li>
            <li class="nav-item"><a href="index.html#contact" class="nav-link">Contact</a></li>
            <li class="nav-item"><a href="register.php" class="nav-link active btn">Register</a></li>
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

        <!-- 3rd Row: Contact Number, WhatsApp Number with checkbox -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="phone">Contact Number <span class="required">*</span></label>
              <div class="phone-input-group">
                <select id="phone-country" name="phone_country" class="country-code">
                  <option value="">Select</option>
                  <!-- Africa -->
                  <optgroup label="Africa">
                    <option value="+213" <?php echo get_form_value('phone_country') === '+213' ? 'selected' : ''; ?>>+213 Algeria</option>
                    <option value="+244" <?php echo get_form_value('phone_country') === '+244' ? 'selected' : ''; ?>>+244 Angola</option>
                    <option value="+229" <?php echo get_form_value('phone_country') === '+229' ? 'selected' : ''; ?>>+229 Benin</option>
                    <option value="+267" <?php echo get_form_value('phone_country') === '+267' ? 'selected' : ''; ?>>+267 Botswana</option>
                    <option value="+226" <?php echo get_form_value('phone_country') === '+226' ? 'selected' : ''; ?>>+226 Burkina Faso</option>
                    <option value="+257" <?php echo get_form_value('phone_country') === '+257' ? 'selected' : ''; ?>>+257 Burundi</option>
                    <option value="+237" <?php echo get_form_value('phone_country') === '+237' ? 'selected' : ''; ?>>+237 Cameroon</option>
                    <option value="+238" <?php echo get_form_value('phone_country') === '+238' ? 'selected' : ''; ?>>+238 Cape Verde</option>
                    <option value="+235" <?php echo get_form_value('phone_country') === '+235' ? 'selected' : ''; ?>>+235 Chad</option>
                    <option value="+269" <?php echo get_form_value('phone_country') === '+269' ? 'selected' : ''; ?>>+269 Comoros</option>
                    <option value="+242" <?php echo get_form_value('phone_country') === '+242' ? 'selected' : ''; ?>>+242 Congo (Republic)</option>
                    <option value="+243" <?php echo get_form_value('phone_country') === '+243' ? 'selected' : ''; ?>>+243 Congo (DRC)</option>
                    <option value="+253" <?php echo get_form_value('phone_country') === '+253' ? 'selected' : ''; ?>>+253 Djibouti</option>
                    <option value="+20" <?php echo get_form_value('phone_country') === '+20' ? 'selected' : ''; ?>>+20 Egypt</option>
                    <option value="+291" <?php echo get_form_value('phone_country') === '+291' ? 'selected' : ''; ?>>+291 Eritrea</option>
                    <option value="+251" <?php echo get_form_value('phone_country') === '+251' ? 'selected' : ''; ?>>+251 Ethiopia</option>
                    <option value="+220" <?php echo get_form_value('phone_country') === '+220' ? 'selected' : ''; ?>>+220 Gambia</option>
                    <option value="+233" <?php echo get_form_value('phone_country') === '+233' ? 'selected' : ''; ?>>+233 Ghana</option>
                    <option value="+224" <?php echo get_form_value('phone_country') === '+224' ? 'selected' : ''; ?>>+224 Guinea</option>
                    <option value="+245" <?php echo get_form_value('phone_country') === '+245' ? 'selected' : ''; ?>>+245 Guinea-Bissau</option>
                    <option value="+225" <?php echo get_form_value('phone_country') === '+225' ? 'selected' : ''; ?>>+225 Ivory Coast</option>
                    <option value="+254" <?php echo get_form_value('phone_country') === '+254' ? 'selected' : ''; ?>>+254 Kenya</option>
                    <option value="+266" <?php echo get_form_value('phone_country') === '+266' ? 'selected' : ''; ?>>+266 Lesotho</option>
                    <option value="+231" <?php echo get_form_value('phone_country') === '+231' ? 'selected' : ''; ?>>+231 Liberia</option>
                    <option value="+218" <?php echo get_form_value('phone_country') === '+218' ? 'selected' : ''; ?>>+218 Libya</option>
                    <option value="+261" <?php echo get_form_value('phone_country') === '+261' ? 'selected' : ''; ?>>+261 Madagascar</option>
                    <option value="+265" <?php echo get_form_value('phone_country') === '+265' ? 'selected' : ''; ?>>+265 Malawi</option>
                    <option value="+223" <?php echo get_form_value('phone_country') === '+223' ? 'selected' : ''; ?>>+223 Mali</option>
                    <option value="+222" <?php echo get_form_value('phone_country') === '+222' ? 'selected' : ''; ?>>+222 Mauritania</option>
                    <option value="+230" <?php echo get_form_value('phone_country') === '+230' ? 'selected' : ''; ?>>+230 Mauritius</option>
                    <option value="+212" <?php echo get_form_value('phone_country') === '+212' ? 'selected' : ''; ?>>+212 Morocco</option>
                    <option value="+258" <?php echo get_form_value('phone_country') === '+258' ? 'selected' : ''; ?>>+258 Mozambique</option>
                    <option value="+264" <?php echo get_form_value('phone_country') === '+264' ? 'selected' : ''; ?>>+264 Namibia</option>
                    <option value="+227" <?php echo get_form_value('phone_country') === '+227' ? 'selected' : ''; ?>>+227 Niger</option>
                    <option value="+234" <?php echo get_form_value('phone_country') === '+234' ? 'selected' : ''; ?>>+234 Nigeria</option>
                    <option value="+250" <?php echo get_form_value('phone_country') === '+250' ? 'selected' : ''; ?>>+250 Rwanda</option>
                    <option value="+221" <?php echo get_form_value('phone_country') === '+221' ? 'selected' : ''; ?>>+221 Senegal</option>
                    <option value="+248" <?php echo get_form_value('phone_country') === '+248' ? 'selected' : ''; ?>>+248 Seychelles</option>
                    <option value="+232" <?php echo get_form_value('phone_country') === '+232' ? 'selected' : ''; ?>>+232 Sierra Leone</option>
                    <option value="+252" <?php echo get_form_value('phone_country') === '+252' ? 'selected' : ''; ?>>+252 Somalia</option>
                    <option value="+27" <?php echo get_form_value('phone_country') === '+27' ? 'selected' : ''; ?>>+27 South Africa</option>
                    <option value="+211" <?php echo get_form_value('phone_country') === '+211' ? 'selected' : ''; ?>>+211 South Sudan</option>
                    <option value="+249" <?php echo get_form_value('phone_country') === '+249' ? 'selected' : ''; ?>>+249 Sudan</option>
                    <option value="+255" <?php echo get_form_value('phone_country') === '+255' ? 'selected' : ''; ?>>+255 Tanzania</option>
                    <option value="+228" <?php echo get_form_value('phone_country') === '+228' ? 'selected' : ''; ?>>+228 Togo</option>
                    <option value="+216" <?php echo get_form_value('phone_country') === '+216' ? 'selected' : ''; ?>>+216 Tunisia</option>
                    <option value="+256" <?php echo get_form_value('phone_country') === '+256' ? 'selected' : ''; ?>>+256 Uganda</option>
                    <option value="+260" <?php echo get_form_value('phone_country') === '+260' ? 'selected' : ''; ?>>+260 Zambia</option>
                    <option value="+263" <?php echo get_form_value('phone_country') === '+263' ? 'selected' : ''; ?>>+263 Zimbabwe</option>
                  </optgroup>
                  <!-- Asia -->
                  <optgroup label="Asia">
                    <option value="+93" <?php echo get_form_value('phone_country') === '+93' ? 'selected' : ''; ?>>+93 Afghanistan</option>
                    <option value="+374" <?php echo get_form_value('phone_country') === '+374' ? 'selected' : ''; ?>>+374 Armenia</option>
                    <option value="+994" <?php echo get_form_value('phone_country') === '+994' ? 'selected' : ''; ?>>+994 Azerbaijan</option>
                    <option value="+973" <?php echo get_form_value('phone_country') === '+973' ? 'selected' : ''; ?>>+973 Bahrain</option>
                    <option value="+880" <?php echo get_form_value('phone_country') === '+880' ? 'selected' : ''; ?>>+880 Bangladesh</option>
                    <option value="+975" <?php echo get_form_value('phone_country') === '+975' ? 'selected' : ''; ?>>+975 Bhutan</option>
                    <option value="+673" <?php echo get_form_value('phone_country') === '+673' ? 'selected' : ''; ?>>+673 Brunei</option>
                    <option value="+855" <?php echo get_form_value('phone_country') === '+855' ? 'selected' : ''; ?>>+855 Cambodia</option>
                    <option value="+86" <?php echo get_form_value('phone_country') === '+86' ? 'selected' : ''; ?>>+86 China</option>
                    <option value="+357" <?php echo get_form_value('phone_country') === '+357' ? 'selected' : ''; ?>>+357 Cyprus</option>
                    <option value="+995" <?php echo get_form_value('phone_country') === '+995' ? 'selected' : ''; ?>>+995 Georgia</option>
                    <option value="+852" <?php echo get_form_value('phone_country') === '+852' ? 'selected' : ''; ?>>+852 Hong Kong</option>
                    <option value="+91" <?php echo get_form_value('phone_country') === '+91' ? 'selected' : ''; ?>>+91 India</option>
                    <option value="+62" <?php echo get_form_value('phone_country') === '+62' ? 'selected' : ''; ?>>+62 Indonesia</option>
                    <option value="+98" <?php echo get_form_value('phone_country') === '+98' ? 'selected' : ''; ?>>+98 Iran</option>
                    <option value="+964" <?php echo get_form_value('phone_country') === '+964' ? 'selected' : ''; ?>>+964 Iraq</option>
                    <option value="+972" <?php echo get_form_value('phone_country') === '+972' ? 'selected' : ''; ?>>+972 Israel</option>
                    <option value="+81" <?php echo get_form_value('phone_country') === '+81' ? 'selected' : ''; ?>>+81 Japan</option>
                    <option value="+962" <?php echo get_form_value('phone_country') === '+962' ? 'selected' : ''; ?>>+962 Jordan</option>
                    <option value="+7" <?php echo get_form_value('phone_country') === '+7' ? 'selected' : ''; ?>>+7 Kazakhstan</option>
                    <option value="+965" <?php echo get_form_value('phone_country') === '+965' ? 'selected' : ''; ?>>+965 Kuwait</option>
                    <option value="+996" <?php echo get_form_value('phone_country') === '+996' ? 'selected' : ''; ?>>+996 Kyrgyzstan</option>
                    <option value="+856" <?php echo get_form_value('phone_country') === '+856' ? 'selected' : ''; ?>>+856 Laos</option>
                    <option value="+961" <?php echo get_form_value('phone_country') === '+961' ? 'selected' : ''; ?>>+961 Lebanon</option>
                    <option value="+853" <?php echo get_form_value('phone_country') === '+853' ? 'selected' : ''; ?>>+853 Macau</option>
                    <option value="+60" <?php echo get_form_value('phone_country') === '+60' ? 'selected' : ''; ?>>+60 Malaysia</option>
                    <option value="+960" <?php echo get_form_value('phone_country') === '+960' ? 'selected' : ''; ?>>+960 Maldives</option>
                    <option value="+976" <?php echo get_form_value('phone_country') === '+976' ? 'selected' : ''; ?>>+976 Mongolia</option>
                    <option value="+95" <?php echo get_form_value('phone_country') === '+95' ? 'selected' : ''; ?>>+95 Myanmar</option>
                    <option value="+977" <?php echo get_form_value('phone_country') === '+977' ? 'selected' : ''; ?>>+977 Nepal</option>
                    <option value="+850" <?php echo get_form_value('phone_country') === '+850' ? 'selected' : ''; ?>>+850 North Korea</option>
                    <option value="+968" <?php echo get_form_value('phone_country') === '+968' ? 'selected' : ''; ?>>+968 Oman</option>
                    <option value="+92" <?php echo get_form_value('phone_country') === '+92' ? 'selected' : ''; ?>>+92 Pakistan</option>
                    <option value="+63" <?php echo get_form_value('phone_country') === '+63' ? 'selected' : ''; ?>>+63 Philippines</option>
                    <option value="+974" <?php echo get_form_value('phone_country') === '+974' ? 'selected' : ''; ?>>+974 Qatar</option>
                    <option value="+966" <?php echo get_form_value('phone_country') === '+966' ? 'selected' : ''; ?>>+966 Saudi Arabia</option>
                    <option value="+65" <?php echo get_form_value('phone_country') === '+65' ? 'selected' : ''; ?>>+65 Singapore</option>
                    <option value="+82" <?php echo get_form_value('phone_country') === '+82' ? 'selected' : ''; ?>>+82 South Korea</option>
                    <option value="+94" <?php echo get_form_value('phone_country') === '+94' ? 'selected' : ''; ?>>+94 Sri Lanka</option>
                    <option value="+963" <?php echo get_form_value('phone_country') === '+963' ? 'selected' : ''; ?>>+963 Syria</option>
                    <option value="+886" <?php echo get_form_value('phone_country') === '+886' ? 'selected' : ''; ?>>+886 Taiwan</option>
                    <option value="+992" <?php echo get_form_value('phone_country') === '+992' ? 'selected' : ''; ?>>+992 Tajikistan</option>
                    <option value="+66" <?php echo get_form_value('phone_country') === '+66' ? 'selected' : ''; ?>>+66 Thailand</option>
                    <option value="+90" <?php echo get_form_value('phone_country') === '+90' ? 'selected' : ''; ?>>+90 Turkey</option>
                    <option value="+993" <?php echo get_form_value('phone_country') === '+993' ? 'selected' : ''; ?>>+993 Turkmenistan</option>
                    <option value="+971" <?php echo get_form_value('phone_country') === '+971' ? 'selected' : ''; ?>>+971 UAE</option>
                    <option value="+998" <?php echo get_form_value('phone_country') === '+998' ? 'selected' : ''; ?>>+998 Uzbekistan</option>
                    <option value="+84" <?php echo get_form_value('phone_country') === '+84' ? 'selected' : ''; ?>>+84 Vietnam</option>
                    <option value="+967" <?php echo get_form_value('phone_country') === '+967' ? 'selected' : ''; ?>>+967 Yemen</option>
                  </optgroup>
                  <!-- Europe -->
                  <optgroup label="Europe">
                    <option value="+355" <?php echo get_form_value('phone_country') === '+355' ? 'selected' : ''; ?>>+355 Albania</option>
                    <option value="+376" <?php echo get_form_value('phone_country') === '+376' ? 'selected' : ''; ?>>+376 Andorra</option>
                    <option value="+43" <?php echo get_form_value('phone_country') === '+43' ? 'selected' : ''; ?>>+43 Austria</option>
                    <option value="+375" <?php echo get_form_value('phone_country') === '+375' ? 'selected' : ''; ?>>+375 Belarus</option>
                    <option value="+32" <?php echo get_form_value('phone_country') === '+32' ? 'selected' : ''; ?>>+32 Belgium</option>
                    <option value="+387" <?php echo get_form_value('phone_country') === '+387' ? 'selected' : ''; ?>>+387 Bosnia & Herzegovina</option>
                    <option value="+359" <?php echo get_form_value('phone_country') === '+359' ? 'selected' : ''; ?>>+359 Bulgaria</option>
                    <option value="+385" <?php echo get_form_value('phone_country') === '+385' ? 'selected' : ''; ?>>+385 Croatia</option>
                    <option value="+420" <?php echo get_form_value('phone_country') === '+420' ? 'selected' : ''; ?>>+420 Czech Republic</option>
                    <option value="+45" <?php echo get_form_value('phone_country') === '+45' ? 'selected' : ''; ?>>+45 Denmark</option>
                    <option value="+372" <?php echo get_form_value('phone_country') === '+372' ? 'selected' : ''; ?>>+372 Estonia</option>
                    <option value="+358" <?php echo get_form_value('phone_country') === '+358' ? 'selected' : ''; ?>>+358 Finland</option>
                    <option value="+33" <?php echo get_form_value('phone_country') === '+33' ? 'selected' : ''; ?>>+33 France</option>
                    <option value="+49" <?php echo get_form_value('phone_country') === '+49' ? 'selected' : ''; ?>>+49 Germany</option>
                    <option value="+30" <?php echo get_form_value('phone_country') === '+30' ? 'selected' : ''; ?>>+30 Greece</option>
                    <option value="+36" <?php echo get_form_value('phone_country') === '+36' ? 'selected' : ''; ?>>+36 Hungary</option>
                    <option value="+354" <?php echo get_form_value('phone_country') === '+354' ? 'selected' : ''; ?>>+354 Iceland</option>
                    <option value="+353" <?php echo get_form_value('phone_country') === '+353' ? 'selected' : ''; ?>>+353 Ireland</option>
                    <option value="+39" <?php echo get_form_value('phone_country') === '+39' ? 'selected' : ''; ?>>+39 Italy</option>
                    <option value="+383" <?php echo get_form_value('phone_country') === '+383' ? 'selected' : ''; ?>>+383 Kosovo</option>
                    <option value="+371" <?php echo get_form_value('phone_country') === '+371' ? 'selected' : ''; ?>>+371 Latvia</option>
                    <option value="+423" <?php echo get_form_value('phone_country') === '+423' ? 'selected' : ''; ?>>+423 Liechtenstein</option>
                    <option value="+370" <?php echo get_form_value('phone_country') === '+370' ? 'selected' : ''; ?>>+370 Lithuania</option>
                    <option value="+352" <?php echo get_form_value('phone_country') === '+352' ? 'selected' : ''; ?>>+352 Luxembourg</option>
                    <option value="+356" <?php echo get_form_value('phone_country') === '+356' ? 'selected' : ''; ?>>+356 Malta</option>
                    <option value="+373" <?php echo get_form_value('phone_country') === '+373' ? 'selected' : ''; ?>>+373 Moldova</option>
                    <option value="+377" <?php echo get_form_value('phone_country') === '+377' ? 'selected' : ''; ?>>+377 Monaco</option>
                    <option value="+382" <?php echo get_form_value('phone_country') === '+382' ? 'selected' : ''; ?>>+382 Montenegro</option>
                    <option value="+31" <?php echo get_form_value('phone_country') === '+31' ? 'selected' : ''; ?>>+31 Netherlands</option>
                    <option value="+389" <?php echo get_form_value('phone_country') === '+389' ? 'selected' : ''; ?>>+389 North Macedonia</option>
                    <option value="+47" <?php echo get_form_value('phone_country') === '+47' ? 'selected' : ''; ?>>+47 Norway</option>
                    <option value="+48" <?php echo get_form_value('phone_country') === '+48' ? 'selected' : ''; ?>>+48 Poland</option>
                    <option value="+351" <?php echo get_form_value('phone_country') === '+351' ? 'selected' : ''; ?>>+351 Portugal</option>
                    <option value="+40" <?php echo get_form_value('phone_country') === '+40' ? 'selected' : ''; ?>>+40 Romania</option>
                    <option value="+7" <?php echo get_form_value('phone_country') === '+7' ? 'selected' : ''; ?>>+7 Russia</option>
                    <option value="+378" <?php echo get_form_value('phone_country') === '+378' ? 'selected' : ''; ?>>+378 San Marino</option>
                    <option value="+381" <?php echo get_form_value('phone_country') === '+381' ? 'selected' : ''; ?>>+381 Serbia</option>
                    <option value="+421" <?php echo get_form_value('phone_country') === '+421' ? 'selected' : ''; ?>>+421 Slovakia</option>
                    <option value="+386" <?php echo get_form_value('phone_country') === '+386' ? 'selected' : ''; ?>>+386 Slovenia</option>
                    <option value="+34" <?php echo get_form_value('phone_country') === '+34' ? 'selected' : ''; ?>>+34 Spain</option>
                    <option value="+46" <?php echo get_form_value('phone_country') === '+46' ? 'selected' : ''; ?>>+46 Sweden</option>
                    <option value="+41" <?php echo get_form_value('phone_country') === '+41' ? 'selected' : ''; ?>>+41 Switzerland</option>
                    <option value="+380" <?php echo get_form_value('phone_country') === '+380' ? 'selected' : ''; ?>>+380 Ukraine</option>
                    <option value="+44" <?php echo get_form_value('phone_country') === '+44' ? 'selected' : ''; ?>>+44 United Kingdom</option>
                    <option value="+379" <?php echo get_form_value('phone_country') === '+379' ? 'selected' : ''; ?>>+379 Vatican City</option>
                  </optgroup>
                  <!-- North America & Caribbean -->
                  <optgroup label="North America & Caribbean">
                    <option value="+1" <?php echo get_form_value('phone_country') === '+1' ? 'selected' : ''; ?>>+1 USA/Canada</option>
                    <option value="+52" <?php echo get_form_value('phone_country') === '+52' ? 'selected' : ''; ?>>+52 Mexico</option>
                    <option value="+1-242" <?php echo get_form_value('phone_country') === '+1-242' ? 'selected' : ''; ?>>+1-242 Bahamas</option>
                    <option value="+1-246" <?php echo get_form_value('phone_country') === '+1-246' ? 'selected' : ''; ?>>+1-246 Barbados</option>
                    <option value="+501" <?php echo get_form_value('phone_country') === '+501' ? 'selected' : ''; ?>>+501 Belize</option>
                    <option value="+1-441" <?php echo get_form_value('phone_country') === '+1-441' ? 'selected' : ''; ?>>+1-441 Bermuda</option>
                    <option value="+506" <?php echo get_form_value('phone_country') === '+506' ? 'selected' : ''; ?>>+506 Costa Rica</option>
                    <option value="+53" <?php echo get_form_value('phone_country') === '+53' ? 'selected' : ''; ?>>+53 Cuba</option>
                    <option value="+1-809" <?php echo get_form_value('phone_country') === '+1-809' ? 'selected' : ''; ?>>+1-809 Dominican Republic</option>
                    <option value="+1-829" <?php echo get_form_value('phone_country') === '+1-829' ? 'selected' : ''; ?>>+1-829 Dominican Republic</option>
                    <option value="+503" <?php echo get_form_value('phone_country') === '+503' ? 'selected' : ''; ?>>+503 El Salvador</option>
                    <option value="+502" <?php echo get_form_value('phone_country') === '+502' ? 'selected' : ''; ?>>+502 Guatemala</option>
                    <option value="+509" <?php echo get_form_value('phone_country') === '+509' ? 'selected' : ''; ?>>+509 Haiti</option>
                    <option value="+504" <?php echo get_form_value('phone_country') === '+504' ? 'selected' : ''; ?>>+504 Honduras</option>
                    <option value="+1-876" <?php echo get_form_value('phone_country') === '+1-876' ? 'selected' : ''; ?>>+1-876 Jamaica</option>
                    <option value="+505" <?php echo get_form_value('phone_country') === '+505' ? 'selected' : ''; ?>>+505 Nicaragua</option>
                    <option value="+507" <?php echo get_form_value('phone_country') === '+507' ? 'selected' : ''; ?>>+507 Panama</option>
                    <option value="+1-787" <?php echo get_form_value('phone_country') === '+1-787' ? 'selected' : ''; ?>>+1-787 Puerto Rico</option>
                    <option value="+1-868" <?php echo get_form_value('phone_country') === '+1-868' ? 'selected' : ''; ?>>+1-868 Trinidad & Tobago</option>
                  </optgroup>
                  <!-- South America -->
                  <optgroup label="South America">
                    <option value="+54" <?php echo get_form_value('phone_country') === '+54' ? 'selected' : ''; ?>>+54 Argentina</option>
                    <option value="+591" <?php echo get_form_value('phone_country') === '+591' ? 'selected' : ''; ?>>+591 Bolivia</option>
                    <option value="+55" <?php echo get_form_value('phone_country') === '+55' ? 'selected' : ''; ?>>+55 Brazil</option>
                    <option value="+56" <?php echo get_form_value('phone_country') === '+56' ? 'selected' : ''; ?>>+56 Chile</option>
                    <option value="+57" <?php echo get_form_value('phone_country') === '+57' ? 'selected' : ''; ?>>+57 Colombia</option>
                    <option value="+593" <?php echo get_form_value('phone_country') === '+593' ? 'selected' : ''; ?>>+593 Ecuador</option>
                    <option value="+592" <?php echo get_form_value('phone_country') === '+592' ? 'selected' : ''; ?>>+592 Guyana</option>
                    <option value="+595" <?php echo get_form_value('phone_country') === '+595' ? 'selected' : ''; ?>>+595 Paraguay</option>
                    <option value="+51" <?php echo get_form_value('phone_country') === '+51' ? 'selected' : ''; ?>>+51 Peru</option>
                    <option value="+597" <?php echo get_form_value('phone_country') === '+597' ? 'selected' : ''; ?>>+597 Suriname</option>
                    <option value="+598" <?php echo get_form_value('phone_country') === '+598' ? 'selected' : ''; ?>>+598 Uruguay</option>
                    <option value="+58" <?php echo get_form_value('phone_country') === '+58' ? 'selected' : ''; ?>>+58 Venezuela</option>
                  </optgroup>
                  <!-- Oceania -->
                  <optgroup label="Oceania">
                    <option value="+61" <?php echo get_form_value('phone_country') === '+61' ? 'selected' : ''; ?>>+61 Australia</option>
                    <option value="+679" <?php echo get_form_value('phone_country') === '+679' ? 'selected' : ''; ?>>+679 Fiji</option>
                    <option value="+686" <?php echo get_form_value('phone_country') === '+686' ? 'selected' : ''; ?>>+686 Kiribati</option>
                    <option value="+692" <?php echo get_form_value('phone_country') === '+692' ? 'selected' : ''; ?>>+692 Marshall Islands</option>
                    <option value="+691" <?php echo get_form_value('phone_country') === '+691' ? 'selected' : ''; ?>>+691 Micronesia</option>
                    <option value="+674" <?php echo get_form_value('phone_country') === '+674' ? 'selected' : ''; ?>>+674 Nauru</option>
                    <option value="+64" <?php echo get_form_value('phone_country') === '+64' ? 'selected' : ''; ?>>+64 New Zealand</option>
                    <option value="+680" <?php echo get_form_value('phone_country') === '+680' ? 'selected' : ''; ?>>+680 Palau</option>
                    <option value="+675" <?php echo get_form_value('phone_country') === '+675' ? 'selected' : ''; ?>>+675 Papua New Guinea</option>
                    <option value="+685" <?php echo get_form_value('phone_country') === '+685' ? 'selected' : ''; ?>>+685 Samoa</option>
                    <option value="+677" <?php echo get_form_value('phone_country') === '+677' ? 'selected' : ''; ?>>+677 Solomon Islands</option>
                    <option value="+676" <?php echo get_form_value('phone_country') === '+676' ? 'selected' : ''; ?>>+676 Tonga</option>
                    <option value="+688" <?php echo get_form_value('phone_country') === '+688' ? 'selected' : ''; ?>>+688 Tuvalu</option>
                    <option value="+678" <?php echo get_form_value('phone_country') === '+678' ? 'selected' : ''; ?>>+678 Vanuatu</option>
                  </optgroup>
                </select>
                <input type="tel" id="phone" name="phone" class="phone-number" required placeholder="Enter phone number" value="<?php echo htmlspecialchars(get_form_value('phone')); ?>">
              </div>
              <div class="error-message" id="phone-error">Please enter a valid phone number</div>
            </div>
          </div>
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
        </div>

        <!-- 4th Row: Years of Expertise, Domain of Expertise -->
        <div class="row">
          <div class="col-50">
            <div class="form-group">
              <label for="whatsapp">WhatsApp Number <span class="required">*</span></label>
              <div class="phone-input-group">
                <select id="whatsapp-country" name="whatsapp_country" class="country-code">
                  <option value="">Select</option>
                  <!-- Africa -->
                  <optgroup label="Africa">
                    <option value="+213" <?php echo get_form_value('whatsapp_country') === '+213' ? 'selected' : ''; ?>>+213 Algeria</option>
                    <option value="+244" <?php echo get_form_value('whatsapp_country') === '+244' ? 'selected' : ''; ?>>+244 Angola</option>
                    <option value="+229" <?php echo get_form_value('whatsapp_country') === '+229' ? 'selected' : ''; ?>>+229 Benin</option>
                    <option value="+267" <?php echo get_form_value('whatsapp_country') === '+267' ? 'selected' : ''; ?>>+267 Botswana</option>
                    <option value="+226" <?php echo get_form_value('whatsapp_country') === '+226' ? 'selected' : ''; ?>>+226 Burkina Faso</option>
                    <option value="+257" <?php echo get_form_value('whatsapp_country') === '+257' ? 'selected' : ''; ?>>+257 Burundi</option>
                    <option value="+237" <?php echo get_form_value('whatsapp_country') === '+237' ? 'selected' : ''; ?>>+237 Cameroon</option>
                    <option value="+238" <?php echo get_form_value('whatsapp_country') === '+238' ? 'selected' : ''; ?>>+238 Cape Verde</option>
                    <option value="+235" <?php echo get_form_value('whatsapp_country') === '+235' ? 'selected' : ''; ?>>+235 Chad</option>
                    <option value="+269" <?php echo get_form_value('whatsapp_country') === '+269' ? 'selected' : ''; ?>>+269 Comoros</option>
                    <option value="+242" <?php echo get_form_value('whatsapp_country') === '+242' ? 'selected' : ''; ?>>+242 Congo (Republic)</option>
                    <option value="+243" <?php echo get_form_value('whatsapp_country') === '+243' ? 'selected' : ''; ?>>+243 Congo (DRC)</option>
                    <option value="+253" <?php echo get_form_value('whatsapp_country') === '+253' ? 'selected' : ''; ?>>+253 Djibouti</option>
                    <option value="+20" <?php echo get_form_value('whatsapp_country') === '+20' ? 'selected' : ''; ?>>+20 Egypt</option>
                    <option value="+291" <?php echo get_form_value('whatsapp_country') === '+291' ? 'selected' : ''; ?>>+291 Eritrea</option>
                    <option value="+251" <?php echo get_form_value('whatsapp_country') === '+251' ? 'selected' : ''; ?>>+251 Ethiopia</option>
                    <option value="+220" <?php echo get_form_value('whatsapp_country') === '+220' ? 'selected' : ''; ?>>+220 Gambia</option>
                    <option value="+233" <?php echo get_form_value('whatsapp_country') === '+233' ? 'selected' : ''; ?>>+233 Ghana</option>
                    <option value="+224" <?php echo get_form_value('whatsapp_country') === '+224' ? 'selected' : ''; ?>>+224 Guinea</option>
                    <option value="+245" <?php echo get_form_value('whatsapp_country') === '+245' ? 'selected' : ''; ?>>+245 Guinea-Bissau</option>
                    <option value="+225" <?php echo get_form_value('whatsapp_country') === '+225' ? 'selected' : ''; ?>>+225 Ivory Coast</option>
                    <option value="+254" <?php echo get_form_value('whatsapp_country') === '+254' ? 'selected' : ''; ?>>+254 Kenya</option>
                    <option value="+266" <?php echo get_form_value('whatsapp_country') === '+266' ? 'selected' : ''; ?>>+266 Lesotho</option>
                    <option value="+231" <?php echo get_form_value('whatsapp_country') === '+231' ? 'selected' : ''; ?>>+231 Liberia</option>
                    <option value="+218" <?php echo get_form_value('whatsapp_country') === '+218' ? 'selected' : ''; ?>>+218 Libya</option>
                    <option value="+261" <?php echo get_form_value('whatsapp_country') === '+261' ? 'selected' : ''; ?>>+261 Madagascar</option>
                    <option value="+265" <?php echo get_form_value('whatsapp_country') === '+265' ? 'selected' : ''; ?>>+265 Malawi</option>
                    <option value="+223" <?php echo get_form_value('whatsapp_country') === '+223' ? 'selected' : ''; ?>>+223 Mali</option>
                    <option value="+222" <?php echo get_form_value('whatsapp_country') === '+222' ? 'selected' : ''; ?>>+222 Mauritania</option>
                    <option value="+230" <?php echo get_form_value('whatsapp_country') === '+230' ? 'selected' : ''; ?>>+230 Mauritius</option>
                    <option value="+212" <?php echo get_form_value('whatsapp_country') === '+212' ? 'selected' : ''; ?>>+212 Morocco</option>
                    <option value="+258" <?php echo get_form_value('whatsapp_country') === '+258' ? 'selected' : ''; ?>>+258 Mozambique</option>
                    <option value="+264" <?php echo get_form_value('whatsapp_country') === '+264' ? 'selected' : ''; ?>>+264 Namibia</option>
                    <option value="+227" <?php echo get_form_value('whatsapp_country') === '+227' ? 'selected' : ''; ?>>+227 Niger</option>
                    <option value="+234" <?php echo get_form_value('whatsapp_country') === '+234' ? 'selected' : ''; ?>>+234 Nigeria</option>
                    <option value="+250" <?php echo get_form_value('whatsapp_country') === '+250' ? 'selected' : ''; ?>>+250 Rwanda</option>
                    <option value="+221" <?php echo get_form_value('whatsapp_country') === '+221' ? 'selected' : ''; ?>>+221 Senegal</option>
                    <option value="+248" <?php echo get_form_value('whatsapp_country') === '+248' ? 'selected' : ''; ?>>+248 Seychelles</option>
                    <option value="+232" <?php echo get_form_value('whatsapp_country') === '+232' ? 'selected' : ''; ?>>+232 Sierra Leone</option>
                    <option value="+252" <?php echo get_form_value('whatsapp_country') === '+252' ? 'selected' : ''; ?>>+252 Somalia</option>
                    <option value="+27" <?php echo get_form_value('whatsapp_country') === '+27' ? 'selected' : ''; ?>>+27 South Africa</option>
                    <option value="+211" <?php echo get_form_value('whatsapp_country') === '+211' ? 'selected' : ''; ?>>+211 South Sudan</option>
                    <option value="+249" <?php echo get_form_value('whatsapp_country') === '+249' ? 'selected' : ''; ?>>+249 Sudan</option>
                    <option value="+255" <?php echo get_form_value('whatsapp_country') === '+255' ? 'selected' : ''; ?>>+255 Tanzania</option>
                    <option value="+228" <?php echo get_form_value('whatsapp_country') === '+228' ? 'selected' : ''; ?>>+228 Togo</option>
                    <option value="+216" <?php echo get_form_value('whatsapp_country') === '+216' ? 'selected' : ''; ?>>+216 Tunisia</option>
                    <option value="+256" <?php echo get_form_value('whatsapp_country') === '+256' ? 'selected' : ''; ?>>+256 Uganda</option>
                    <option value="+260" <?php echo get_form_value('whatsapp_country') === '+260' ? 'selected' : ''; ?>>+260 Zambia</option>
                    <option value="+263" <?php echo get_form_value('whatsapp_country') === '+263' ? 'selected' : ''; ?>>+263 Zimbabwe</option>
                  </optgroup>
                  <!-- Asia -->
                  <optgroup label="Asia">
                    <option value="+93" <?php echo get_form_value('whatsapp_country') === '+93' ? 'selected' : ''; ?>>+93 Afghanistan</option>
                    <option value="+374" <?php echo get_form_value('whatsapp_country') === '+374' ? 'selected' : ''; ?>>+374 Armenia</option>
                    <option value="+994" <?php echo get_form_value('whatsapp_country') === '+994' ? 'selected' : ''; ?>>+994 Azerbaijan</option>
                    <option value="+973" <?php echo get_form_value('whatsapp_country') === '+973' ? 'selected' : ''; ?>>+973 Bahrain</option>
                    <option value="+880" <?php echo get_form_value('whatsapp_country') === '+880' ? 'selected' : ''; ?>>+880 Bangladesh</option>
                    <option value="+975" <?php echo get_form_value('whatsapp_country') === '+975' ? 'selected' : ''; ?>>+975 Bhutan</option>
                    <option value="+673" <?php echo get_form_value('whatsapp_country') === '+673' ? 'selected' : ''; ?>>+673 Brunei</option>
                    <option value="+855" <?php echo get_form_value('whatsapp_country') === '+855' ? 'selected' : ''; ?>>+855 Cambodia</option>
                    <option value="+86" <?php echo get_form_value('whatsapp_country') === '+86' ? 'selected' : ''; ?>>+86 China</option>
                    <option value="+357" <?php echo get_form_value('whatsapp_country') === '+357' ? 'selected' : ''; ?>>+357 Cyprus</option>
                    <option value="+995" <?php echo get_form_value('whatsapp_country') === '+995' ? 'selected' : ''; ?>>+995 Georgia</option>
                    <option value="+852" <?php echo get_form_value('whatsapp_country') === '+852' ? 'selected' : ''; ?>>+852 Hong Kong</option>
                    <option value="+91" <?php echo get_form_value('whatsapp_country') === '+91' ? 'selected' : ''; ?>>+91 India</option>
                    <option value="+62" <?php echo get_form_value('whatsapp_country') === '+62' ? 'selected' : ''; ?>>+62 Indonesia</option>
                    <option value="+98" <?php echo get_form_value('whatsapp_country') === '+98' ? 'selected' : ''; ?>>+98 Iran</option>
                    <option value="+964" <?php echo get_form_value('whatsapp_country') === '+964' ? 'selected' : ''; ?>>+964 Iraq</option>
                    <option value="+972" <?php echo get_form_value('whatsapp_country') === '+972' ? 'selected' : ''; ?>>+972 Israel</option>
                    <option value="+81" <?php echo get_form_value('whatsapp_country') === '+81' ? 'selected' : ''; ?>>+81 Japan</option>
                    <option value="+962" <?php echo get_form_value('whatsapp_country') === '+962' ? 'selected' : ''; ?>>+962 Jordan</option>
                    <option value="+7" <?php echo get_form_value('whatsapp_country') === '+7' ? 'selected' : ''; ?>>+7 Kazakhstan</option>
                    <option value="+965" <?php echo get_form_value('whatsapp_country') === '+965' ? 'selected' : ''; ?>>+965 Kuwait</option>
                    <option value="+996" <?php echo get_form_value('whatsapp_country') === '+996' ? 'selected' : ''; ?>>+996 Kyrgyzstan</option>
                    <option value="+856" <?php echo get_form_value('whatsapp_country') === '+856' ? 'selected' : ''; ?>>+856 Laos</option>
                    <option value="+961" <?php echo get_form_value('whatsapp_country') === '+961' ? 'selected' : ''; ?>>+961 Lebanon</option>
                    <option value="+853" <?php echo get_form_value('whatsapp_country') === '+853' ? 'selected' : ''; ?>>+853 Macau</option>
                    <option value="+60" <?php echo get_form_value('whatsapp_country') === '+60' ? 'selected' : ''; ?>>+60 Malaysia</option>
                    <option value="+960" <?php echo get_form_value('whatsapp_country') === '+960' ? 'selected' : ''; ?>>+960 Maldives</option>
                    <option value="+976" <?php echo get_form_value('whatsapp_country') === '+976' ? 'selected' : ''; ?>>+976 Mongolia</option>
                    <option value="+95" <?php echo get_form_value('whatsapp_country') === '+95' ? 'selected' : ''; ?>>+95 Myanmar</option>
                    <option value="+977" <?php echo get_form_value('whatsapp_country') === '+977' ? 'selected' : ''; ?>>+977 Nepal</option>
                    <option value="+850" <?php echo get_form_value('whatsapp_country') === '+850' ? 'selected' : ''; ?>>+850 North Korea</option>
                    <option value="+968" <?php echo get_form_value('whatsapp_country') === '+968' ? 'selected' : ''; ?>>+968 Oman</option>
                    <option value="+92" <?php echo get_form_value('whatsapp_country') === '+92' ? 'selected' : ''; ?>>+92 Pakistan</option>
                    <option value="+63" <?php echo get_form_value('whatsapp_country') === '+63' ? 'selected' : ''; ?>>+63 Philippines</option>
                    <option value="+974" <?php echo get_form_value('whatsapp_country') === '+974' ? 'selected' : ''; ?>>+974 Qatar</option>
                    <option value="+966" <?php echo get_form_value('whatsapp_country') === '+966' ? 'selected' : ''; ?>>+966 Saudi Arabia</option>
                    <option value="+65" <?php echo get_form_value('whatsapp_country') === '+65' ? 'selected' : ''; ?>>+65 Singapore</option>
                    <option value="+82" <?php echo get_form_value('whatsapp_country') === '+82' ? 'selected' : ''; ?>>+82 South Korea</option>
                    <option value="+94" <?php echo get_form_value('whatsapp_country') === '+94' ? 'selected' : ''; ?>>+94 Sri Lanka</option>
                    <option value="+963" <?php echo get_form_value('whatsapp_country') === '+963' ? 'selected' : ''; ?>>+963 Syria</option>
                    <option value="+886" <?php echo get_form_value('whatsapp_country') === '+886' ? 'selected' : ''; ?>>+886 Taiwan</option>
                    <option value="+992" <?php echo get_form_value('whatsapp_country') === '+992' ? 'selected' : ''; ?>>+992 Tajikistan</option>
                    <option value="+66" <?php echo get_form_value('whatsapp_country') === '+66' ? 'selected' : ''; ?>>+66 Thailand</option>
                    <option value="+90" <?php echo get_form_value('whatsapp_country') === '+90' ? 'selected' : ''; ?>>+90 Turkey</option>
                    <option value="+993" <?php echo get_form_value('whatsapp_country') === '+993' ? 'selected' : ''; ?>>+993 Turkmenistan</option>
                    <option value="+971" <?php echo get_form_value('whatsapp_country') === '+971' ? 'selected' : ''; ?>>+971 UAE</option>
                    <option value="+998" <?php echo get_form_value('whatsapp_country') === '+998' ? 'selected' : ''; ?>>+998 Uzbekistan</option>
                    <option value="+84" <?php echo get_form_value('whatsapp_country') === '+84' ? 'selected' : ''; ?>>+84 Vietnam</option>
                    <option value="+967" <?php echo get_form_value('whatsapp_country') === '+967' ? 'selected' : ''; ?>>+967 Yemen</option>
                  </optgroup>
                  <!-- Europe -->
                  <optgroup label="Europe">
                    <option value="+355" <?php echo get_form_value('whatsapp_country') === '+355' ? 'selected' : ''; ?>>+355 Albania</option>
                    <option value="+376" <?php echo get_form_value('whatsapp_country') === '+376' ? 'selected' : ''; ?>>+376 Andorra</option>
                    <option value="+43" <?php echo get_form_value('whatsapp_country') === '+43' ? 'selected' : ''; ?>>+43 Austria</option>
                    <option value="+375" <?php echo get_form_value('whatsapp_country') === '+375' ? 'selected' : ''; ?>>+375 Belarus</option>
                    <option value="+32" <?php echo get_form_value('whatsapp_country') === '+32' ? 'selected' : ''; ?>>+32 Belgium</option>
                    <option value="+387" <?php echo get_form_value('whatsapp_country') === '+387' ? 'selected' : ''; ?>>+387 Bosnia & Herzegovina</option>
                    <option value="+359" <?php echo get_form_value('whatsapp_country') === '+359' ? 'selected' : ''; ?>>+359 Bulgaria</option>
                    <option value="+385" <?php echo get_form_value('whatsapp_country') === '+385' ? 'selected' : ''; ?>>+385 Croatia</option>
                    <option value="+420" <?php echo get_form_value('whatsapp_country') === '+420' ? 'selected' : ''; ?>>+420 Czech Republic</option>
                    <option value="+45" <?php echo get_form_value('whatsapp_country') === '+45' ? 'selected' : ''; ?>>+45 Denmark</option>
                    <option value="+372" <?php echo get_form_value('whatsapp_country') === '+372' ? 'selected' : ''; ?>>+372 Estonia</option>
                    <option value="+358" <?php echo get_form_value('whatsapp_country') === '+358' ? 'selected' : ''; ?>>+358 Finland</option>
                    <option value="+33" <?php echo get_form_value('whatsapp_country') === '+33' ? 'selected' : ''; ?>>+33 France</option>
                    <option value="+49" <?php echo get_form_value('whatsapp_country') === '+49' ? 'selected' : ''; ?>>+49 Germany</option>
                    <option value="+30" <?php echo get_form_value('whatsapp_country') === '+30' ? 'selected' : ''; ?>>+30 Greece</option>
                    <option value="+36" <?php echo get_form_value('whatsapp_country') === '+36' ? 'selected' : ''; ?>>+36 Hungary</option>
                    <option value="+354" <?php echo get_form_value('whatsapp_country') === '+354' ? 'selected' : ''; ?>>+354 Iceland</option>
                    <option value="+353" <?php echo get_form_value('whatsapp_country') === '+353' ? 'selected' : ''; ?>>+353 Ireland</option>
                    <option value="+39" <?php echo get_form_value('whatsapp_country') === '+39' ? 'selected' : ''; ?>>+39 Italy</option>
                    <option value="+383" <?php echo get_form_value('whatsapp_country') === '+383' ? 'selected' : ''; ?>>+383 Kosovo</option>
                    <option value="+371" <?php echo get_form_value('whatsapp_country') === '+371' ? 'selected' : ''; ?>>+371 Latvia</option>
                    <option value="+423" <?php echo get_form_value('whatsapp_country') === '+423' ? 'selected' : ''; ?>>+423 Liechtenstein</option>
                    <option value="+370" <?php echo get_form_value('whatsapp_country') === '+370' ? 'selected' : ''; ?>>+370 Lithuania</option>
                    <option value="+352" <?php echo get_form_value('whatsapp_country') === '+352' ? 'selected' : ''; ?>>+352 Luxembourg</option>
                    <option value="+356" <?php echo get_form_value('whatsapp_country') === '+356' ? 'selected' : ''; ?>>+356 Malta</option>
                    <option value="+373" <?php echo get_form_value('whatsapp_country') === '+373' ? 'selected' : ''; ?>>+373 Moldova</option>
                    <option value="+377" <?php echo get_form_value('whatsapp_country') === '+377' ? 'selected' : ''; ?>>+377 Monaco</option>
                    <option value="+382" <?php echo get_form_value('whatsapp_country') === '+382' ? 'selected' : ''; ?>>+382 Montenegro</option>
                    <option value="+31" <?php echo get_form_value('whatsapp_country') === '+31' ? 'selected' : ''; ?>>+31 Netherlands</option>
                    <option value="+389" <?php echo get_form_value('whatsapp_country') === '+389' ? 'selected' : ''; ?>>+389 North Macedonia</option>
                    <option value="+47" <?php echo get_form_value('whatsapp_country') === '+47' ? 'selected' : ''; ?>>+47 Norway</option>
                    <option value="+48" <?php echo get_form_value('whatsapp_country') === '+48' ? 'selected' : ''; ?>>+48 Poland</option>
                    <option value="+351" <?php echo get_form_value('whatsapp_country') === '+351' ? 'selected' : ''; ?>>+351 Portugal</option>
                    <option value="+40" <?php echo get_form_value('whatsapp_country') === '+40' ? 'selected' : ''; ?>>+40 Romania</option>
                    <option value="+7" <?php echo get_form_value('whatsapp_country') === '+7' ? 'selected' : ''; ?>>+7 Russia</option>
                    <option value="+378" <?php echo get_form_value('whatsapp_country') === '+378' ? 'selected' : ''; ?>>+378 San Marino</option>
                    <option value="+381" <?php echo get_form_value('whatsapp_country') === '+381' ? 'selected' : ''; ?>>+381 Serbia</option>
                    <option value="+421" <?php echo get_form_value('whatsapp_country') === '+421' ? 'selected' : ''; ?>>+421 Slovakia</option>
                    <option value="+386" <?php echo get_form_value('whatsapp_country') === '+386' ? 'selected' : ''; ?>>+386 Slovenia</option>
                    <option value="+34" <?php echo get_form_value('whatsapp_country') === '+34' ? 'selected' : ''; ?>>+34 Spain</option>
                    <option value="+46" <?php echo get_form_value('whatsapp_country') === '+46' ? 'selected' : ''; ?>>+46 Sweden</option>
                    <option value="+41" <?php echo get_form_value('whatsapp_country') === '+41' ? 'selected' : ''; ?>>+41 Switzerland</option>
                    <option value="+380" <?php echo get_form_value('whatsapp_country') === '+380' ? 'selected' : ''; ?>>+380 Ukraine</option>
                    <option value="+44" <?php echo get_form_value('whatsapp_country') === '+44' ? 'selected' : ''; ?>>+44 United Kingdom</option>
                    <option value="+379" <?php echo get_form_value('whatsapp_country') === '+379' ? 'selected' : ''; ?>>+379 Vatican City</option>
                  </optgroup>
                  <!-- North America & Caribbean -->
                  <optgroup label="North America & Caribbean">
                    <option value="+1" <?php echo get_form_value('whatsapp_country') === '+1' ? 'selected' : ''; ?>>+1 USA/Canada</option>
                    <option value="+52" <?php echo get_form_value('whatsapp_country') === '+52' ? 'selected' : ''; ?>>+52 Mexico</option>
                    <option value="+1-242" <?php echo get_form_value('whatsapp_country') === '+1-242' ? 'selected' : ''; ?>>+1-242 Bahamas</option>
                    <option value="+1-246" <?php echo get_form_value('whatsapp_country') === '+1-246' ? 'selected' : ''; ?>>+1-246 Barbados</option>
                    <option value="+501" <?php echo get_form_value('whatsapp_country') === '+501' ? 'selected' : ''; ?>>+501 Belize</option>
                    <option value="+1-441" <?php echo get_form_value('whatsapp_country') === '+1-441' ? 'selected' : ''; ?>>+1-441 Bermuda</option>
                    <option value="+506" <?php echo get_form_value('whatsapp_country') === '+506' ? 'selected' : ''; ?>>+506 Costa Rica</option>
                    <option value="+53" <?php echo get_form_value('whatsapp_country') === '+53' ? 'selected' : ''; ?>>+53 Cuba</option>
                    <option value="+1-809" <?php echo get_form_value('whatsapp_country') === '+1-809' ? 'selected' : ''; ?>>+1-809 Dominican Republic</option>
                    <option value="+1-829" <?php echo get_form_value('whatsapp_country') === '+1-829' ? 'selected' : ''; ?>>+1-829 Dominican Republic</option>
                    <option value="+503" <?php echo get_form_value('whatsapp_country') === '+503' ? 'selected' : ''; ?>>+503 El Salvador</option>
                    <option value="+502" <?php echo get_form_value('whatsapp_country') === '+502' ? 'selected' : ''; ?>>+502 Guatemala</option>
                    <option value="+509" <?php echo get_form_value('whatsapp_country') === '+509' ? 'selected' : ''; ?>>+509 Haiti</option>
                    <option value="+504" <?php echo get_form_value('whatsapp_country') === '+504' ? 'selected' : ''; ?>>+504 Honduras</option>
                    <option value="+1-876" <?php echo get_form_value('whatsapp_country') === '+1-876' ? 'selected' : ''; ?>>+1-876 Jamaica</option>
                    <option value="+505" <?php echo get_form_value('whatsapp_country') === '+505' ? 'selected' : ''; ?>>+505 Nicaragua</option>
                    <option value="+507" <?php echo get_form_value('whatsapp_country') === '+507' ? 'selected' : ''; ?>>+507 Panama</option>
                    <option value="+1-787" <?php echo get_form_value('whatsapp_country') === '+1-787' ? 'selected' : ''; ?>>+1-787 Puerto Rico</option>
                    <option value="+1-868" <?php echo get_form_value('whatsapp_country') === '+1-868' ? 'selected' : ''; ?>>+1-868 Trinidad & Tobago</option>
                  </optgroup>
                  <!-- South America -->
                  <optgroup label="South America">
                    <option value="+54" <?php echo get_form_value('whatsapp_country') === '+54' ? 'selected' : ''; ?>>+54 Argentina</option>
                    <option value="+591" <?php echo get_form_value('whatsapp_country') === '+591' ? 'selected' : ''; ?>>+591 Bolivia</option>
                    <option value="+55" <?php echo get_form_value('whatsapp_country') === '+55' ? 'selected' : ''; ?>>+55 Brazil</option>
                    <option value="+56" <?php echo get_form_value('whatsapp_country') === '+56' ? 'selected' : ''; ?>>+56 Chile</option>
                    <option value="+57" <?php echo get_form_value('whatsapp_country') === '+57' ? 'selected' : ''; ?>>+57 Colombia</option>
                    <option value="+593" <?php echo get_form_value('whatsapp_country') === '+593' ? 'selected' : ''; ?>>+593 Ecuador</option>
                    <option value="+592" <?php echo get_form_value('whatsapp_country') === '+592' ? 'selected' : ''; ?>>+592 Guyana</option>
                    <option value="+595" <?php echo get_form_value('whatsapp_country') === '+595' ? 'selected' : ''; ?>>+595 Paraguay</option>
                    <option value="+51" <?php echo get_form_value('whatsapp_country') === '+51' ? 'selected' : ''; ?>>+51 Peru</option>
                    <option value="+597" <?php echo get_form_value('whatsapp_country') === '+597' ? 'selected' : ''; ?>>+597 Suriname</option>
                    <option value="+598" <?php echo get_form_value('whatsapp_country') === '+598' ? 'selected' : ''; ?>>+598 Uruguay</option>
                    <option value="+58" <?php echo get_form_value('whatsapp_country') === '+58' ? 'selected' : ''; ?>>+58 Venezuela</option>
                  </optgroup>
                  <!-- Oceania -->
                  <optgroup label="Oceania">
                    <option value="+61" <?php echo get_form_value('whatsapp_country') === '+61' ? 'selected' : ''; ?>>+61 Australia</option>
                    <option value="+679" <?php echo get_form_value('whatsapp_country') === '+679' ? 'selected' : ''; ?>>+679 Fiji</option>
                    <option value="+686" <?php echo get_form_value('whatsapp_country') === '+686' ? 'selected' : ''; ?>>+686 Kiribati</option>
                    <option value="+692" <?php echo get_form_value('whatsapp_country') === '+692' ? 'selected' : ''; ?>>+692 Marshall Islands</option>
                    <option value="+691" <?php echo get_form_value('whatsapp_country') === '+691' ? 'selected' : ''; ?>>+691 Micronesia</option>
                    <option value="+674" <?php echo get_form_value('whatsapp_country') === '+674' ? 'selected' : ''; ?>>+674 Nauru</option>
                    <option value="+64" <?php echo get_form_value('whatsapp_country') === '+64' ? 'selected' : ''; ?>>+64 New Zealand</option>
                    <option value="+680" <?php echo get_form_value('whatsapp_country') === '+680' ? 'selected' : ''; ?>>+680 Palau</option>
                    <option value="+675" <?php echo get_form_value('whatsapp_country') === '+675' ? 'selected' : ''; ?>>+675 Papua New Guinea</option>
                    <option value="+685" <?php echo get_form_value('whatsapp_country') === '+685' ? 'selected' : ''; ?>>+685 Samoa</option>
                    <option value="+677" <?php echo get_form_value('whatsapp_country') === '+677' ? 'selected' : ''; ?>>+677 Solomon Islands</option>
                    <option value="+676" <?php echo get_form_value('whatsapp_country') === '+676' ? 'selected' : ''; ?>>+676 Tonga</option>
                    <option value="+688" <?php echo get_form_value('whatsapp_country') === '+688' ? 'selected' : ''; ?>>+688 Tuvalu</option>
                    <option value="+678" <?php echo get_form_value('whatsapp_country') === '+678' ? 'selected' : ''; ?>>+678 Vanuatu</option>
                  </optgroup>
                </select>
                <input type="tel" id="whatsapp" name="whatsapp" class="phone-number" required placeholder="Enter WhatsApp number" value="<?php echo htmlspecialchars(get_form_value('whatsapp')); ?>">
              </div>
              <div class="error-message" id="whatsapp-error">Please enter a valid WhatsApp number</div>
              
              <!-- Checkbox to copy contact number to WhatsApp -->
              <div class="checkbox-group">
                <input type="checkbox" id="sameAsPhone" name="sameAsPhone" <?php echo get_form_value('sameAsPhone') ? 'checked' : ''; ?>>
                <label for="sameAsPhone">Same as Contact Number</label>
              </div>
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
        <div class="row">
        </div>
        <!-- Event Questions - Each in separate rows -->
        <div class="row">
          <div class="col-100">
            <div class="form-group">
              <label>Are you interested in PERFORMING in the cultural events?</label>
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
              <label>Are you interested in PARTICIPATING in the Gaming events?</label>
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
              <label>Are you interested in PARTICIPATING in the Tech Music events?</label>
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

        <div class="button-group">
          <button type="submit" class="btn btn-primary" style="flex: 1; padding: 16px;">
            <span class="icon">✅</span>
            Submit Registration
          </button>
        </div>
      </form>
    </div>
  </div>

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
      
      // Try to play the video
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
      const phoneCountrySelect = document.getElementById('phone-country');
      const phoneInput = document.getElementById('phone');
      const whatsappCountrySelect = document.getElementById('whatsapp-country');
      const whatsappInput = document.getElementById('whatsapp');

      sameAsPhoneCheckbox.addEventListener('change', function() {
        if (this.checked) {
          // If checked, copy contact number to WhatsApp and disable the field
          whatsappCountrySelect.value = phoneCountrySelect.value;
          whatsappInput.value = phoneInput.value;
          whatsappInput.setAttribute('disabled', 'disabled');
          whatsappInput.style.backgroundColor = '#555'; // Visual cue that it's disabled
          whatsappCountrySelect.setAttribute('disabled', 'disabled');
          whatsappCountrySelect.style.backgroundColor = '#555';
        } else {
          // If unchecked, enable the field and clear its value
          whatsappInput.removeAttribute('disabled');
          whatsappInput.style.backgroundColor = ''; // Reset to original style
          whatsappCountrySelect.removeAttribute('disabled');
          whatsappCountrySelect.style.backgroundColor = '';
          whatsappInput.value = ''; // Clear the value
        }
      });

      // Also, update WhatsApp number if contact number changes while the checkbox is checked
      phoneInput.addEventListener('input', function() {
        if (sameAsPhoneCheckbox.checked) {
          whatsappInput.value = phoneInput.value;
        }
      });

      phoneCountrySelect.addEventListener('change', function() {
        if (sameAsPhoneCheckbox.checked) {
          whatsappCountrySelect.value = phoneCountrySelect.value;
        }
      });
      
      // Validation functions
      function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
      }

      function validatePhone(phone) {
        // Remove all non-digit characters except + at the beginning
        const cleaned = phone.replace(/[^\d+]/g, '');
        
        // Check if it starts with + (international) or has at least 7 digits
        if (/^\+/.test(cleaned)) {
          // International number - should have 8-15 digits after +
          return cleaned.length >= 8 && cleaned.length <= 16;
        } else {
          // Local number - should have 7-15 digits
          return cleaned.length >= 7 && cleaned.length <= 15;
        }
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

      document.getElementById('phone').addEventListener('blur', function() {
        if (!validatePhone(this.value)) {
          showError('phone', 'phone-error');
        } else {
          hideError('phone', 'phone-error');
        }
      });

      document.getElementById('whatsapp').addEventListener('blur', function() {
        if (!validatePhone(this.value)) {
          showError('whatsapp', 'whatsapp-error');
        } else {
          hideError('whatsapp', 'whatsapp-error');
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
        
        // Phone validation
        if (!validatePhone(document.getElementById('phone').value)) {
          showError('phone', 'phone-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'phone';
        }
        
        // WhatsApp validation
        if (!validatePhone(document.getElementById('whatsapp').value)) {
          showError('whatsapp', 'whatsapp-error');
          isValid = false;
          if (!firstErrorField) firstErrorField = 'whatsapp';
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
</body>
</html>