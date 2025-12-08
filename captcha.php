<?php
// captcha.php - Generates a CAPTCHA and stores it in the session.

// Start the session to store the CAPTCHA value.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to generate a CAPTCHA image.
function generateCaptchaImage($text) {
    // Image dimensions
    $width = 150;
    $height = 40;
    
    // Create the image resource.
    $image = imagecreatetruecolor($width, $height);
    if (!$image) {
        // Handle GD library error.
        error_log("Failed to create image resource. Check if GD is enabled.");
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Error: Could not generate CAPTCHA image.';
        exit;
    }
    
    // Define colors.
    $backgroundColor = imagecolorallocate($image, 42, 42, 21); // Dark background
    $textColor = imagecolorallocate($image, 255, 204, 0); // Yellow text
    $lineColor = imagecolorallocate($image, 0, 168, 255); // Blue lines
    
    // Fill the background.
    imagefill($image, 0, 0, $backgroundColor);
    
    // Add some random lines for noise.
    for ($i = 0; $i < 5; $i++) {
        imageline($image, 
            rand(0, $width), 
            rand(0, $height), 
            rand(0, $width), 
            rand(0, $height), 
            $lineColor);
    }
    
    // Add the text to the image.
    // Try to find a TrueType font, or fall back to built-in GD font
    $fontPath = null;
    
    // Common font paths to try
    $possibleFontPaths = [
        dirname(__FILE__) . '/arial.ttf',
        dirname(__FILE__) . '/fonts/arial.ttf',
        'C:/Windows/Fonts/arial.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
    ];
    
    foreach ($possibleFontPaths as $path) {
        if (file_exists($path)) {
            $fontPath = $path;
            break;
        }
    }
    
    if ($fontPath && function_exists('imagettftext')) {
        // Use TrueType font with imagettftext()
        $fontSize = 18;
        
        // Calculate text position to center it
        $textBoundingBox = imagettfbbox($fontSize, 0, $fontPath, $text);
        if (!$textBoundingBox) {
            // Fall back to built-in font
            error_log("Failed to get text bounding box with font: $fontPath");
        } else {
            $textWidth = $textBoundingBox[2] - $textBoundingBox[0];
            $textHeight = $textBoundingBox[1] - $textBoundingBox[7];
            $x = ($width - $textWidth) / 2;
            $y = ($height + $textHeight) / 2;
            
            // CORRECTED: Proper parameter order for imagettftext()
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);
            
            // Output the image
            header('Content-Type: image/png');
            imagepng($image);
            imagedestroy($image);
            exit();
        }
    }
    
    // Fallback: Use built-in GD font with imagestring()
    $font = 5; // Built-in font (1-5, 5 is largest)
    $fontWidth = imagefontwidth($font);
    $fontHeight = imagefontheight($font);
    $textWidth = strlen($text) * $fontWidth;
    $textHeight = $fontHeight;
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Tell the browser this is a PNG image.
    header('Content-Type: image/png');
    
    // Output the image.
    imagepng($image);
    
    // Free up memory.
    imagedestroy($image);
    exit();
}

// Main logic to generate and store the CAPTCHA.
if (isset($_GET['generate']) && $_GET['generate'] === 'true') {
    // Randomly choose a character set: 0=uppercase, 1=lowercase, 2=numbers.
    $charSetType = rand(0, 2);
    $chars = '';
    
    if ($charSetType === 0) {
        // Uppercase letters only.
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    } elseif ($charSetType === 1) {
        // Lowercase letters only.
        $chars = 'abcdefghijklmnopqrstuvwxyz';
    } else {
        // Numbers only.
        $chars = '0123456789';
    }
    
    // Generate a 6-character string.
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    // Store the generated text in the session for validation.
    $_SESSION['captcha_text'] = $captcha;
    
    // Generate the image.
    generateCaptchaImage($captcha);
}