<?php
// db_connect.php - Database connection configuration

// Database credentials - Update these with your actual credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alumni_meet');

// Create connection
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        // Log error instead of displaying it in production
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    
    // Set charset to utf8mb4 for full Unicode support
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Close connection
function db_close($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Check if email already exists
function email_exists($email) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }
    
    $stmt = $conn->prepare("SELECT id FROM alumni WHERE email = ?");
    if ($stmt === false) {
        db_close($conn);
        return false;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    
    $stmt->close();
    db_close($conn);
    
    return $exists;
}

// Save alumni data
function save_alumni($data) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }
    
    // Combine country code with phone number
    $phone = ($data['phone_country'] ?? '') . ($data['phone'] ?? '');
    $whatsapp = ($data['whatsapp_country'] ?? '') . ($data['whatsapp'] ?? '');
    
    $stmt = $conn->prepare("INSERT INTO alumni (
        name, gender, email, dob, graduation_year, phone, whatsapp, 
        years_of_expertise, domain, current_position, current_company, 
        current_address, linkedin, participating_with_family, family_count, 
        interested_in_cultural, interested_in_gaming, interested_in_tech_music, 
        registration_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt === false) {
        db_close($conn);
        return false;
    }
    
    $stmt->bind_param(
        "sssssssssssssissss",
        $data['name'], $data['gender'], $data['email'], $data['dob'], 
        $data['graduation_year'], $phone, $whatsapp,
        $data['years_of_expertise'], $data['domain'], $data['current_position'], 
        $data['current_company'], $data['current_address'], $data['linkedin'], 
        $data['participating_with_family'], $data['family_count'],
        $data['interested_in_cultural'], $data['interested_in_gaming'], 
        $data['interested_in_tech_music']
    );
    
    $success = $stmt->execute();
    
    $stmt->close();
    db_close($conn);
    
    return $success;
}
?>