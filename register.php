<?php
// Start the session
session_start();

// Handle notification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_email'])) {
    $email = filter_var($_POST['notify_email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Here you would typically save the email to your database
        // For demo purposes, we'll just show success message
        
        // You could add:
        // file_put_contents('notifications.txt', $email . PHP_EOL, FILE_APPEND);
        // Or insert into database: INSERT INTO notifications (email) VALUES ('$email')
        
        $notification_sent = true;
    } else {
        $error_message = "Please enter a valid email address";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>B.Sc. CSD Alumni Meet - Registration Opening Soon</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Russo+One&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --neon-yellow: #FFD700;
      --neon-yellow-glow: rgba(255, 215, 0, 0.4);
      --gold-yellow: #FFC107;
      --ivory-white: #FFFFF0;
      --dark-bg: #000000;
      --bg-color: #2a2a15;
      --text-color: #ffcc00;
      --card-bg: rgba(42, 42, 21, 0.15);
      --input-bg: rgba(42, 42, 21, 0.4);
      --input-border: #3a506b;
      --button-bg: #ffcc00;
      --button-text: #000000;
      --success-color: #4CAF50;
      --error-color: #ff3366;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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
      padding-top: 100px;
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
      transition: all 0.3s ease;
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
      transition: all 0.3s ease;
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

    /* Fallback background */
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
      font-family: 'Audiowide', cursive;
      font-size: 2.8rem;
      margin-bottom: 10px;
      color: var(--text-color);
      text-transform: uppercase;
      letter-spacing: 3px;
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
      font-family: 'Russo One', sans-serif;
      color: #fff;
      font-size: 1.2rem;
      margin-top: 15px;
      font-weight: 600;
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
      padding: 40px 20px;
      background: rgba(42, 42, 21, 0.2);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
    }

    .coming-soon-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .coming-soon-title {
      font-family: 'Audiowide', cursive;
      font-size: 3.5rem;
      margin-bottom: 20px;
      color: var(--neon-yellow);
      text-transform: uppercase;
      letter-spacing: 3px;
      text-shadow:
        1px 1px 2px #000,
        2px 2px 4px #000,
        3px 3px 6px rgba(0, 0, 0, 0.9),
        4px 4px 8px rgba(0, 0, 0, 0.8),
        0 0 20px var(--neon-yellow),
        0 0 40px var(--gold-yellow),
        0 0 60px rgba(255, 215, 0, 0.5);
      animation: pulse 2s infinite;
    }

    .coming-soon-message {
      font-family: 'Roboto', sans-serif;
      font-size: 1.3rem;
      margin-bottom: 30px;
      color: var(--ivory-white);
      max-width: 700px;
      line-height: 1.6;
    }

    .notify-container {
      margin-top: 30px;
      width: 100%;
      max-width: 500px;
    }

    .notify-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .notify-input {
      padding: 15px;
      border: 1px solid var(--input-border);
      border-radius: 8px;
      background: var(--input-bg);
      color: var(--text-color);
      font-size: 1rem;
      outline: none;
      transition: all 0.3s ease;
    }

    .notify-input:focus {
      border-color: var(--neon-yellow);
      box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3);
    }

    .notify-button {
      padding: 15px;
      background: var(--button-bg);
      color: var(--button-text);
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .notify-button:hover {
      background: #ffdd33;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255, 204, 0, 0.5);
    }

    .notify-button:disabled {
      background: var(--success-color);
      cursor: not-allowed;
      transform: none;
    }

    .success-message {
      color: var(--success-color);
      font-size: 0.9rem;
      margin-top: 10px;
      display: none;
      padding: 10px;
      background: rgba(76, 175, 80, 0.1);
      border-radius: 5px;
      border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .error-message {
      color: var(--error-color);
      font-size: 0.9rem;
      margin-top: 10px;
      padding: 10px;
      background: rgba(255, 51, 102, 0.1);
      border-radius: 5px;
      border: 1px solid rgba(255, 51, 102, 0.3);
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    /* Mobile layout */
    @media (max-width: 768px) {
      .container {
        width: 95%;
      }
      
      .coming-soon-title {
        font-size: 2.5rem;
      }
      
      .coming-soon-message {
        font-size: 1.1rem;
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
        transition: all 0.3s ease;
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
        <li class="nav-item"><a href="index.html#home" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
        <li class="nav-item"><a href="index.html#reboot-explanation" class="nav-link">REBOOT 40</a></li>
        <li class="nav-item"><a href="memories.php" class="nav-link">Memories</a></li>
        <li class="nav-item"><a href="gallery.html" class="nav-link">Gallery</a></li>
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

  <!-- Main Coming Soon Page -->
  <div class="container">
    <div class="header">
      <div class="header-content">
        <h1>ALUMNI MEET</h1>
        <p>B.Sc. Computer Systems and Design</p>
      </div>
    </div>

    <div class="form-container">
      <div class="coming-soon-container">
        <h2 class="coming-soon-title">REGISTRATION OPENING SOON</h2>
        <p class="coming-soon-message">
          We're excited to welcome our alumni to the B.Sc. Computer Systems and Design Alumni Meet! 
          Registration will be opening soon. Please check back later to register for this special event.
        </p>
        
        <div class="notify-container">

          
          <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
          <?php endif; ?>
          
          <?php if (isset($notification_sent)): ?>
            <div class="success-message">
              <i class="fas fa-check-circle"></i> Thank you! We'll notify you when registration opens.
            </div>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
      
      // Handle video playback
      const video = document.getElementById('bgVideo');
      
      // Try to play the video
      video.play().catch(function(error) {
        console.log("Video autoplay failed:", error);
      });
      
      // Handle video errors
      video.addEventListener('error', function() {
        console.log("Video loading error");
      });
    });
  </script>
</body>
</html>
