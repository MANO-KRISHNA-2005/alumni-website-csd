<?php
// security_helpers.php - Complete security helpers with CSRF protection

/**
 * Generate a CSRF token and store it in the session
 */
function generate_csrf_token() {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate a new token if one doesn't exist or is expired
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate the CSRF token from the request
 * @param string $token The token to validate
 * @param int $max_age Maximum age of the token in seconds (default: 1 hour)
 * @return bool True if valid, false otherwise
 */
function validate_csrf_token($token, $max_age = 3600) {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists in session
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token matches
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    
    // Check if token has expired
    if (time() - $_SESSION['csrf_token_time'] > $max_age) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return true;
}

/**
 * Get the current CSRF token
 */
function get_csrf_token() {
    return generate_csrf_token();
}

/**
 * Clear the CSRF token
 */
function clear_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['csrf_token']);
    unset($_SESSION['csrf_token_time']);
}

/**
 * Store form data in session for persistence after errors
 * @param array $data The form data to store
 */
function store_form_data($data) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['form_data'] = $data;
}

/**
 * Get stored form data from session
 * @return array The stored form data
 */
function get_stored_form_data() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $data = $_SESSION['form_data'] ?? [];
    // Clear the stored data after retrieving
    unset($_SESSION['form_data']);
    return $data;
}

/**
 * Clear stored form data
 */
function clear_stored_form_data() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['form_data']);
}

/**
 * Sanitize input data
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 * @param string $email The email to validate
 * @return bool True if valid, false otherwise
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (international format)
 * @param string $phone The phone number to validate
 * @return bool True if valid, false otherwise
 */
function validate_phone($phone) {
    // Remove all non-digit characters except + at the beginning
    $cleaned = preg_replace('/[^\d+]/', '', $phone);
    
    // Check if it starts with + (international) or has at least 7 digits
    if (preg_match('/^\+/', $cleaned)) {
        // International number - should have 8-15 digits after +
        return strlen($cleaned) >= 8 && strlen($cleaned) <= 16;
    } else {
        // Local number - should have 7-15 digits
        return strlen($cleaned) >= 7 && strlen($cleaned) <= 15;
    }
}

/**
 * Validate year (between 1985 and current year + 5)
 * @param int $year The year to validate
 * @return bool True if valid, false otherwise
 */
function validate_year($year) {
    $current_year = date('Y');
    $min_year = 1985;
    $max_year = $current_year + 5;
    
    return is_numeric($year) && $year >= $min_year && $year <= $max_year;
}

/**
 * Validate LinkedIn URL (optional field)
 * @param string $url The LinkedIn URL to validate
 * @return bool True if valid or empty, false otherwise
 */
function validate_linkedin($url) {
    if (empty($url)) {
        return true; // LinkedIn is optional
    }
    
    // Basic validation for LinkedIn URL
    return (strpos($url, 'linkedin.com') !== false) || 
           (strpos($url, 'http') === 0 && strpos($url, 'linkedin.com') !== false);
}

/**
 * Redirect with error message
 * @param string $error_code The error code to redirect with
 * @param array $form_data The form data to preserve
 */
function redirect_with_error($error_code, $form_data = []) {
    // Store form data before redirecting
    if (!empty($form_data)) {
        store_form_data($form_data);
    }
    
    // Clear any existing error parameters
    $params = [];
    if (isset($_GET['status'])) {
        $params['status'] = $_GET['status'];
    }
    $params['error'] = $error_code;
    
    $query = http_build_query($params);
    header("Location: register.php" . ($query ? "?$query" : ""));
    exit;
}

/**
 * Redirect with success message
 * @param string $message The success message
 */
function redirect_with_success($message = 'Registration successful!') {
    // Clear any stored form data on success
    clear_stored_form_data();
    
    // Clear any existing error parameters
    $params = ['status' => 'success'];
    if (isset($message) && $message !== 'Registration successful!') {
        $params['message'] = $message;
    }
    
    $query = http_build_query($params);
    header("Location: register.php" . ($query ? "?$query" : ""));
    exit;
}

/**
 * Redirect to homepage with success message
 * @param string $message The success message
 */
function redirect_to_homepage($message = 'Registration successful!') {
    // Clear any stored form data on success
    clear_stored_form_data();
    
    // Redirect to homepage with success message
    header("Location: index.html" . urlencode($message));
    exit;
}
?>