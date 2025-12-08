<?php
// register_process.php - Process the alumni registration form

// Start the session FIRST THING
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once 'security_helpers.php';
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_error('invalid_request');
}

// Validate CSRF token with improved check
if (!isset($_POST['csrf_token'])) {
    error_log("Submission failed: CSRF token not present in POST data.");
    clear_csrf_token(); // Clear any existing token
    redirect_with_error('csrf_error');
}

if (!validate_csrf_token($_POST['csrf_token'])) {
    error_log("Submission failed: CSRF token validation failed. Check error logs for details.");
    clear_csrf_token(); // Clear the token to force regeneration
    redirect_with_error('csrf_error');
}

// If we reach here, the CSRF token is valid.
// Clear the token immediately to prevent replay attacks.
clear_csrf_token(); 

// Initialize variables and sanitize inputs
 $form_data = $_POST; // Store all POST data for potential error redirect
 $name = sanitize_input($_POST['name'] ?? '');
 $gender = sanitize_input($_POST['gender'] ?? '');
 $email = sanitize_input($_POST['email'] ?? '');
 $dob = sanitize_input($_POST['dob'] ?? '');
 $pyear = sanitize_input($_POST['pyear'] ?? '');
 $phone = sanitize_input($_POST['phone'] ?? ''); // No validation for phone
 $whatsapp = sanitize_input($_POST['whatsapp'] ?? ''); // No validation for phone
 $sameAsPhone = isset($_POST['sameAsPhone']) ? '1' : '0'; // Capture checkbox state
 $expertise = sanitize_input($_POST['expertise'] ?? '');
 $domain = sanitize_input($_POST['domain'] ?? '');
 $currentPosition = sanitize_input($_POST['currentPosition'] ?? '');
 $currentCompany = sanitize_input($_POST['currentCompany'] ?? '');
 $currentAddress = sanitize_input($_POST['currentAddress'] ?? '');
 $linkedin = sanitize_input($_POST['linkedin'] ?? '');
 $family = sanitize_input($_POST['family'] ?? '');
 $familyCount = sanitize_input($_POST['familyCount'] ?? '0');
 $cultural = sanitize_input($_POST['cultural'] ?? 'No');
 $gaming = sanitize_input($_POST['gaming'] ?? 'No');
 $techmusic = sanitize_input($_POST['techmusic'] ?? 'No');
 $nostalgicMemory = sanitize_input($_POST['nostalgicMemory'] ?? '');

// If "Same as Mobile Number" is checked, copy phone values to WhatsApp
if ($sameAsPhone === '1') {
    $whatsapp = $phone;
}

// Validate required fields (removed phone validation)
 $errors = [];

if (empty($name)) {
    $errors[] = 'missing_fields';
}

if (empty($gender) || !in_array($gender, ['Male', 'Female'])) {
    $errors[] = 'invalid_gender';
}

if (empty($email) || !validate_email($email)) {
    $errors[] = 'invalid_email';
}

if (empty($dob)) {
    $errors[] = 'missing_fields';
}

if (empty($pyear) || !validate_year($pyear)) {
    $errors[] = 'invalid_year';
}

if (empty($expertise)) {
    $errors[] = 'invalid_expertise';
}

if (empty($domain)) {
    $errors[] = 'missing_fields';
}

if (empty($currentPosition)) {
    $errors[] = 'missing_fields';
}

if (empty($currentCompany)) {
    $errors[] = 'missing_fields';
}

if (empty($currentAddress)) {
    $errors[] = 'missing_fields';
}

if (!empty($linkedin) && !validate_linkedin($linkedin)) {
    $errors[] = 'invalid_linkedin';
}

if (empty($family) || !in_array($family, ['Yes', 'No'])) {
    $errors[] = 'missing_fields';
}

if ($family === 'Yes' && (empty($familyCount) || !is_numeric($familyCount) || $familyCount < 1)) {
    $errors[] = 'missing_fields';
}

// CAPTCHA FIX: Validate against the hidden form field, not the session.
 $enteredCaptcha = sanitize_input($_POST['captcha'] ?? '');
 $actualCaptcha = sanitize_input($_POST['captcha_text'] ?? '');

if (empty($enteredCaptcha) || strtolower($enteredCaptcha) !== strtolower($actualCaptcha)) {
    $errors[] = 'captcha_error';
}


// If there are validation errors, redirect with the first error and form data
if (!empty($errors)) {
    redirect_with_error($errors[0], $form_data);
}

// Check if email already exists
if (email_exists($email)) {
    redirect_with_error('duplicate_email', $form_data);
}

// Prepare data for saving
 $alumni_data = [
    'name' => $name,
    'gender' => $gender,
    'email' => $email,
    'dob' => $dob,
    'graduation_year' => $pyear,
    'phone' => $phone,
    'whatsapp' => $whatsapp,
    'years_of_expertise' => $expertise,
    'domain' => $domain,
    'current_position' => $currentPosition,
    'current_company' => $currentCompany,
    'current_address' => $currentAddress,
    'linkedin' => $linkedin,
    'participating_with_family' => $family,
    'family_count' => $familyCount ?: 0,
    'interested_in_cultural' => $cultural,
    'interested_in_gaming' => $gaming,
    'interested_in_tech_music' => $techmusic,
    'nostalgic_memory' => $nostalgicMemory
];

// Save to database
if (save_alumni($alumni_data)) {
    // Clear the CSRF token after successful submission
    clear_csrf_token();
    // Redirect to registration page with success status for popup display
    header("Location: register.php?status=success");
    exit;
} else {
    redirect_with_error('db_error', $form_data);
}
?>