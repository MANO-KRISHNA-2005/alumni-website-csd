<?php
// register_process.php - Process the alumni registration form

// Start the session
session_start();

// Include required files
require_once 'security_helpers.php';
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_error('invalid_request');
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    clear_csrf_token(); // Clear the token to force regeneration
    redirect_with_error('csrf_error', $_POST);
}

// Initialize variables and sanitize inputs
$form_data = $_POST; // Store all POST data for potential error redirect
$name = sanitize_input($_POST['name'] ?? '');
$gender = sanitize_input($_POST['gender'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$dob = sanitize_input($_POST['dob'] ?? '');
$pyear = sanitize_input($_POST['pyear'] ?? '');
$phone_country = sanitize_input($_POST['phone_country'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$whatsapp_country = sanitize_input($_POST['whatsapp_country'] ?? '');
$whatsapp = sanitize_input($_POST['whatsapp'] ?? '');
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

// If "Same as Mobile Number" is checked, copy phone values to WhatsApp
if ($sameAsPhone === '1') {
    $whatsapp_country = $phone_country;
    $whatsapp = $phone;
}

// Combine country code with phone number
$full_phone = $phone_country . $phone;
$full_whatsapp = $whatsapp_country . $whatsapp;

// Validate required fields
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

if (empty($full_phone) || !validate_phone($full_phone)) {
    $errors[] = 'invalid_phone';
}

if (empty($full_whatsapp) || !validate_phone($full_whatsapp)) {
    $errors[] = 'invalid_phone';
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
    'phone_country' => $phone_country,
    'phone' => $phone,
    'whatsapp_country' => $whatsapp_country,
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
    'interested_in_tech_music' => $techmusic
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