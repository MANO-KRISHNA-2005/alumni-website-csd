<?php
// memory_api.php - Secure Memory Wall API
require_once 'db_connect.php'; // Ensure this file exists and connects to your DB

// Security headers
header('Content-Type: application/json');
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// CORS headers (adjust for production)
 $allowed_origins = ['http://localhost', 'https://yourdomain.com']; // Add your actual domain
 $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Rate limiting
function checkRateLimit() {
    // Using a simple file-based rate limiter for demonstration
    // For production, consider Redis or a more robust solution
    $max_requests = 50;
    $window = 3600; // 1 hour
    
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'api_requests_' . md5($client_ip);
    $rate_limit_file = sys_get_temp_dir() . '/' . $key . '.json';
    
    $data = [];
    if (file_exists($rate_limit_file)) {
        $data = json_decode(file_get_contents($rate_limit_file), true);
    }
    
    $now = time();
    if (empty($data) || $now > $data['reset_time']) {
        $data = [
            'count' => 0,
            'reset_time' => $now + $window
        ];
    }
    
    if ($data['count'] >= $max_requests) {
        http_response_code(429);
        echo json_encode(['error' => 'Rate limit exceeded. Please try again later.']);
        exit;
    }
    
    $data['count']++;
    file_put_contents($rate_limit_file, json_encode($data));
}

// Input validation
function validateInput($input) {
    $errors = [];
    
    if (!isset($input['name']) || empty(trim($input['name']))) {
        $errors[] = 'Name is required';
    } else {
        $name = trim($input['name']);
        if (strlen($name) > 100) {
            $errors[] = 'Name too long (max 100 characters)';
        }
        if (!preg_match('/^[a-zA-Z0-9\s\-\.\',]+$/', $name)) {
            $errors[] = 'Name contains invalid characters';
        }
    }
    
    if (!isset($input['message']) || empty(trim($input['message']))) {
        $errors[] = 'Message is required';
    } else {
        $message = trim($input['message']);
        if (strlen($message) > 500) {
            $errors[] = 'Message too long (max 500 characters)';
        }
        if (preg_match('/<script|javascript:|onload=|onerror=/i', $message)) {
            $errors[] = 'Message contains potentially dangerous content';
        }
    }
    
    // Validate optional numeric fields
    $numeric_fields = ['thread', 'ratio', 'x_position', 'y_position'];
    foreach ($numeric_fields as $field) {
        if (isset($input[$field]) && !is_numeric($input[$field])) {
            $errors[] = "Invalid value for $field";
        }
    }
    
    return $errors;
}

// Main API logic
try {
    // Apply rate limiting for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkRateLimit();
    }
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Default action for GET is to retrieve notes
            getMemoryNotes();
            break;
            
        case 'POST':
            addMemoryNote();
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Memory API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function getMemoryNotes() {
    $conn = db_connect();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, message, thread, ratio, x_position, y_position, manual, created_at FROM memory_notes ORDER BY created_at DESC");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $notes = $result->fetch_all(MYSQLI_ASSOC);
    
    // Sanitize output data
    $sanitized_notes = array_map(function($note) {
        return [
            'id' => (int)$note['id'],
            'name' => htmlspecialchars($note['name'], ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($note['message'], ENT_QUOTES, 'UTF-8'),
            'thread' => $note['thread'] !== null ? (int)$note['thread'] : null,
            'ratio' => $note['ratio'] !== null ? (float)$note['ratio'] : null,
            'x_position' => $note['x_position'] !== null ? (int)$note['x_position'] : null,
            'y_position' => $note['y_position'] !== null ? (int)$note['y_position'] : null,
            'manual' => (bool)$note['manual'],
            'created_at' => $note['created_at']
        ];
    }, $notes);
    
    echo json_encode(['success' => true, 'data' => $sanitized_notes]);
    $stmt->close();
    db_close($conn);
}

function addMemoryNote() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }
    
    $errors = validateInput($input);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['error' => 'Validation failed', 'details' => $errors]);
        return;
    }
    
    $conn = db_connect();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Sanitize and prepare data
    $name = trim($conn->real_escape_string($input['name']));
    $message = trim($conn->real_escape_string($input['message']));
    $thread = isset($input['thread']) ? (int)$input['thread'] : null;
    $ratio = isset($input['ratio']) ? (float)$input['ratio'] : null;
    $x_position = isset($input['x_position']) ? (int)$input['x_position'] : null;
    $y_position = isset($input['y_position']) ? (int)$input['y_position'] : null;
    $manual = isset($input['manual']) ? (int)$input['manual'] : 0;

    // Additional business logic validation
    if ($x_position === null || $y_position === null || $thread === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required position or thread data.']);
        return;
    }
    
    if ($x_position < 0 || $x_position > 5000 || $y_position < 0 || $y_position > 5000) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid position coordinates.']);
        return;
    }
    
    // Insert the new note
    $stmt = $conn->prepare("INSERT INTO memory_notes (name, message, thread, ratio, x_position, y_position, manual) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    // 'ssiddii' corresponds to string, string, integer, double, integer, integer, integer
    $stmt->bind_param("ssiddii", $name, $message, $thread, $ratio, $x_position, $y_position, $manual);
    
    if ($stmt->execute()) {
        $noteId = $conn->insert_id;
        
        // Fetch the newly created note to return it
        $stmt_new = $conn->prepare("SELECT id, name, message, thread, ratio, x_position, y_position, manual, created_at FROM memory_notes WHERE id = ?");
        if (!$stmt_new) {
            throw new Exception('Failed to prepare fetch statement: ' . $conn->error);
        }
        
        $stmt_new->bind_param("i", $noteId);
        $stmt_new->execute();
        $result = $stmt_new->get_result();
        $newNote = $result->fetch_assoc();
        
        if ($newNote) {
            $sanitized_note = [
                'id' => (int)$newNote['id'],
                'name' => htmlspecialchars($newNote['name'], ENT_QUOTES, 'UTF-8'),
                'message' => htmlspecialchars($newNote['message'], ENT_QUOTES, 'UTF-8'),
                'thread' => $newNote['thread'] !== null ? (int)$newNote['thread'] : null,
                'ratio' => $newNote['ratio'] !== null ? (float)$newNote['ratio'] : null,
                'x_position' => $newNote['x_position'] !== null ? (int)$newNote['x_position'] : null,
                'y_position' => $newNote['y_position'] !== null ? (int)$newNote['y_position'] : null,
                'manual' => (bool)$newNote['manual'],
                'created_at' => $newNote['created_at']
            ];
            echo json_encode(['success' => true, 'data' => $sanitized_note]);
        } else {
             http_response_code(500);
             echo json_encode(['error' => 'Failed to retrieve the newly created note.']);
        }
        $stmt_new->close();
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add note to database: ' . $stmt->error]);
    }
    
    $stmt->close();
    db_close($conn);
}
?>