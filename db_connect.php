<?php
// db_connect.php - Database connection configuration

// Database credentials - Update these with your actual credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alumni_meet');

/*
-- SQL Query to create the `alumni` table.
-- Run this query in your database (e.g., phpMyAdmin) before using the registration form.

CREATE TABLE `alumni` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `gender` ENUM('Male', 'Female') NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `dob` DATE NOT NULL,
  `graduation_year` YEAR(4) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `whatsapp` VARCHAR(20) NOT NULL,
  `years_of_expertise` VARCHAR(20) NOT NULL,
  `domain` VARCHAR(255) NOT NULL,
  `current_position` VARCHAR(255) NOT NULL,
  `current_company` VARCHAR(255) NOT NULL,
  `current_address` TEXT NOT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `participating_with_family` ENUM('Yes', 'No') NOT NULL,
  `family_count` INT(2) UNSIGNED DEFAULT 0,
  `interested_in_cultural` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `interested_in_gaming` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `interested_in_tech_music` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `nostalgic_memory` TEXT DEFAULT NULL,
  `registration_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

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
    
    // SQL query with 19 placeholders
    $stmt = $conn->prepare("INSERT INTO alumni (
        name, gender, email, dob, graduation_year, phone, whatsapp, 
        years_of_expertise, domain, current_position, current_company, 
        current_address, linkedin, participating_with_family, family_count, 
        interested_in_cultural, interested_in_gaming, interested_in_tech_music, 
        nostalgic_memory
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        // Log the specific SQL error for debugging
        error_log("SQL Prepare Failed: " . $conn->error);
        db_close($conn);
        return false;
    }
    
    // CORRECTED: Count the parameters properly
    // We have 19 parameters: 18 strings + 1 integer (family_count)
    // Position 15 is family_count (integer), all others are strings
    
    $stmt->bind_param(
        "ssssssssssssssissss", // CORRECTED: 19 characters total
        // Breakdown: 14 's' (positions 1-14), 1 'i' (position 15), 4 's' (positions 16-19)
        $data['name'],              // 1 (string)
        $data['gender'],            // 2 (string)
        $data['email'],             // 3 (string)
        $data['dob'],               // 4 (string)
        $data['graduation_year'],   // 5 (string)
        $data['phone'],             // 6 (string)
        $data['whatsapp'],          // 7 (string)
        $data['years_of_expertise'], // 8 (string)
        $data['domain'],            // 9 (string)
        $data['current_position'],  // 10 (string)
        $data['current_company'],   // 11 (string)
        $data['current_address'],   // 12 (string)
        $data['linkedin'],          // 13 (string)
        $data['participating_with_family'], // 14 (string)
        $data['family_count'],      // 15 (integer) - This is the 'i'
        $data['interested_in_cultural'], // 16 (string)
        $data['interested_in_gaming'], // 17 (string)
        $data['interested_in_tech_music'], // 18 (string)
        $data['nostalgic_memory']   // 19 (string)
    );
    
    $success = $stmt->execute();
    
    if (!$success) {
        // Log the specific execution error for debugging
        error_log("SQL Execute Failed: " . $stmt->error);
    }
    
    $stmt->close();
    db_close($conn);
    
    return $success;
}
?>