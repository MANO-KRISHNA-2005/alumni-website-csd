<?php

// ==========================================
// 1. CONFIGURATION & SECURITY
// ==========================================

// --- Session Configuration ---
// Set secure session parameters BEFORE starting the session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Uncomment the line below ONLY after you enable HTTPS on your server
// ini_set('session.cookie_secure', 1);

// Start the session
session_start();

// --- Database Connection ---
// Include the database connection file
require_once 'db_connect.php';

// Get the database connection using the function from db_connect.php
$conn = db_connect();

// Check if the database connection was successful
if (!$conn) {
    die("Error: Could not connect to the database. Please check your db_connect.php file.");
}

// --- Admin Credentials ---
// ⚠️ SECURITY WARNING: Using a plain-text password is highly discouraged.
// This is for temporary development only. Do not use in a production environment.
$admin_user = "admin";
$admin_pass = "brmh@reboot";

// --- Login Attempt & Lockout Configuration ---
// Maximum number of allowed failed login attempts
$max_login_attempts = 3;
// Duration of the lockout in seconds (e.g., 10 minutes)
$login_lockout_duration = 10 * 60; // 10 minutes

// Check if the user is currently locked out
$is_locked_out = false;
if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
    $remaining_time = ceil(($_SESSION['login_locked_until'] - time()) / 60);
    $error_msg = "Too many failed login attempts. Please try again in $remaining_time minutes.";
    $is_locked_out = true;
}

// ==========================================
// 2. BACKEND LOGIC
// ==========================================

// --- Logout Logic ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session completely
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: admin.php");
    exit();
}

// --- Login Logic ---
if (isset($_POST['login_btn']) && !$is_locked_out) {
    // CSRF protection: Validate the token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    
    // Sanitize and validate inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    
    // ⚠️ SECURITY WARNING: This is a plain-text password comparison.
    // It is highly insecure and should not be used in a production environment.
    if ($username === $admin_user && $password === $admin_pass) {
        // Regenerate the session ID to prevent session fixation attacks
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['last_activity'] = time(); // Set the last activity time
        
        // Reset login attempts on successful login
        unset($_SESSION['login_attempts']);
        unset($_SESSION['login_locked_until']);
        
        header("Location: admin.php");
        exit();
    } else {
        // Increment login attempts
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;
        
        // Check if max attempts reached
        if ($_SESSION['login_attempts'] >= $max_login_attempts) {
            $_SESSION['login_locked_until'] = time() + $login_lockout_duration;
            $remaining_time = ceil($login_lockout_duration / 60);
            $error_msg = "Too many failed login attempts. Account locked for $remaining_time minutes.";
        } else {
            $remaining_attempts = $max_login_attempts - $_SESSION['login_attempts'];
            $error_msg = "Invalid username or password. $remaining_attempts attempts remaining.";
        }
        
        // Log the failed login attempt for security monitoring
        error_log("Failed admin login attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    }
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// --- Session Timeout Check ---
// Set the session timeout duration in seconds (e.g., 30 minutes)
$session_timeout_duration = 30 * 60; // 30 minutes

if ($is_logged_in) {
    // Check if the session has expired due to inactivity
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout_duration)) {
        session_unset();
        session_destroy();
        header("Location: admin.php?session_expired=1");
        exit();
    }
    // Update the last activity time on each page load for a logged-in user
    $_SESSION['last_activity'] = time();
}

// --- CSRF Token Generation ---
// Generate a new CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==========================================
// 3. LOGGED-IN ACTIONS (DELETE, UPDATE, EXPORT)
// ==========================================

if ($is_logged_in) {
    
    // --- ACTION: DELETE Record ---
    if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
        // Validate CSRF token
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed");
        }
        
        // Sanitize and validate inputs
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
        
        // Validate the 'type' parameter to prevent SQL injection
        if ($type !== 'alumni' && $type !== 'memory_notes') {
            die("Invalid type parameter");
        }
        
        $table = ($type == 'alumni') ? 'alumni' : 'memory_notes';
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        header("Location: admin.php");
        exit();
    }

    // --- ACTION: UPDATE Alumni Record ---
    if (isset($_POST['update_alumni'])) {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed");
        }
        
        // Sanitize and validate all inputs
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $grad_year = filter_input(INPUT_POST, 'graduation_year', FILTER_VALIDATE_INT);
        $company = filter_input(INPUT_POST, 'current_company', FILTER_SANITIZE_STRING);
        $position = filter_input(INPUT_POST, 'current_position', FILTER_SANITIZE_STRING);
        $family = filter_input(INPUT_POST, 'participating_with_family', FILTER_SANITIZE_STRING);
        $count = filter_input(INPUT_POST, 'family_count', FILTER_VALIDATE_INT);
        
        // Validate the family participation option
        if ($family !== 'Yes' && $family !== 'No') {
            die("Invalid family participation option");
        }
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE alumni SET name=?, email=?, phone=?, graduation_year=?, current_company=?, current_position=?, participating_with_family=?, family_count=? WHERE id=?");
        $stmt->bind_param("sssisssii", $name, $email, $phone, $grad_year, $company, $position, $family, $count, $id);
        $stmt->execute();
        
        header("Location: admin.php");
        exit();
    }

    // --- ACTION: EXPORT Data to CSV ---
    if (isset($_GET['export'])) {
        // Validate CSRF token
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token validation failed");
        }
        
        $export = filter_input(INPUT_GET, 'export', FILTER_SANITIZE_STRING);
        
        // Validate the export parameter
        if ($export !== 'alumni' && $export !== 'memory_notes' && $export !== 'yearwise_stats') {
            die("Invalid export parameter");
        }
        
        if ($export === 'yearwise_stats') {
            // Export year-wise statistics
            $filename = "yearwise_alumni_stats_" . date('Y-m-d') . ".csv";
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            $output = fopen('php://output', 'w');
            
            // Get year-wise statistics
            $stmt = $conn->prepare("SELECT graduation_year, COUNT(*) as alumni_count FROM alumni GROUP BY graduation_year ORDER BY graduation_year ASC");
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Write headers
            fputcsv($output, ['Graduation Year', 'Alumni Count', 'Percentage']);
            
            // Get total alumni count for percentage calculation
            $total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM alumni");
            $total_stmt->execute();
            $total_result = $total_stmt->get_result();
            $total_row = $total_result->fetch_assoc();
            $total_alumni = $total_row['total'];
            
            // Write data
            while ($row = $result->fetch_assoc()) {
                $percentage = $total_alumni > 0 ? round(($row['alumni_count'] / $total_alumni) * 100, 2) : 0;
                fputcsv($output, [
                    $row['graduation_year'],
                    $row['alumni_count'],
                    $percentage . '%'
                ]);
            }
            
            fclose($output);
            exit();
        } else {
            $table = ($export == 'alumni') ? 'alumni' : 'memory_notes';
            $filename = $table . "_export_" . date('Y-m-d') . ".csv";
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            $output = fopen('php://output', 'w');
            
            // Use prepared statements
            $stmt = $conn->prepare("SELECT * FROM $table");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Get column names for headers
                $field_info = $result->fetch_fields();
                $headers = array();
                foreach ($field_info as $field) {
                    $headers[] = $field->name;
                }
                fputcsv($output, $headers);
                
                // Reset result pointer and output data
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
            exit();
        }
    }

    // ==========================================
    // 4. STATISTICAL DATA FETCHING
    // ==========================================
    
    // A. Overview Counts
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_alumni, 
        COALESCE(SUM(family_count), 0) as total_family,
        (COUNT(*) + COALESCE(SUM(family_count), 0)) as total_attendees
        FROM alumni");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM memory_notes");
    $stmt->execute();
    $result = $stmt->get_result();
    $mem_count = $result->fetch_assoc()['count'];

    // B. Graduation Year Distribution (For Graph)
    $year_labels = [];
    $year_data = [];
    $year_stats = []; // For year-wise boxes
    $stmt = $conn->prepare("SELECT graduation_year, COUNT(*) as count FROM alumni GROUP BY graduation_year ORDER BY graduation_year ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $year_labels[] = $row['graduation_year'];
        $year_data[] = $row['count'];
        $year_stats[$row['graduation_year']] = $row['count'];
    }

    // C. Get all years with counts (for detailed view)
    $all_years = [];
    $stmt = $conn->prepare("SELECT graduation_year, COUNT(*) as count FROM alumni GROUP BY graduation_year ORDER BY graduation_year DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $all_years[] = [
            'year' => $row['graduation_year'],
            'count' => $row['count']
        ];
    }

    // D. Registration Timeline (Day wise) - Requires created_at column
    $date_labels = [];
    $date_data = [];
    // Check if created_at column exists, otherwise fallback
    $check_col = $conn->query("SHOW COLUMNS FROM alumni LIKE 'created_at'");
    if($check_col->num_rows > 0) {
        $stmt = $conn->prepare("SELECT DATE(created_at) as reg_date, COUNT(*) as count FROM alumni GROUP BY DATE(created_at) ORDER BY reg_date ASC LIMIT 30");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $date_labels[] = date('M d', strtotime($row['reg_date']));
            $date_data[] = $row['count'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>REBOOT 40 - Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --neon-gold: #FFD700;
            --dark-bg: #050505;
            --card-bg: #141414;
            --text: #ffffff;
            --border: #333;
        }
        
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--dark-bg); color: var(--text); margin: 0; padding: 0; }
        
        .login-wrap { height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle, #222 0%, #000 100%); }
        .login-box { background: rgba(20,20,20,0.9); padding: 40px; border: 1px solid var(--neon-gold); border-radius: 15px; width: 350px; text-align: center; box-shadow: 0 0 30px rgba(255, 215, 0, 0.15); }
        .login-box input { width: 100%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #444; color: white; border-radius: 5px; }
        .login-box button:disabled { background: #666; cursor: not-allowed; }
        
        .navbar { background: rgba(0,0,0,0.95); border-bottom: 1px solid var(--neon-gold); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .brand { font-size: 1.5rem; font-weight: bold; color: var(--neon-gold); letter-spacing: 2px; }
        
        .container { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: var(--card-bg); padding: 25px; border-radius: 12px; border-top: 3px solid var(--neon-gold); box-shadow: 0 5px 15px rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-info h3 { margin: 0; font-size: 2.2rem; color: white; }
        .stat-info p { margin: 5px 0 0; color: #888; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .stat-icon { font-size: 2.5rem; color: var(--neon-gold); opacity: 0.8; }

        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .chart-container { background: var(--card-bg); padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .chart-container h3 { color: var(--neon-gold); margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 10px; }

        .section-header { display: flex; justify-content: space-between; align-items: center; margin: 40px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .table-wrap { overflow-x: auto; background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
        th { background: #111; color: var(--neon-gold); font-size: 0.85rem; text-transform: uppercase; }
        tr:hover { background: rgba(255, 215, 0, 0.05); }

        .btn { padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.3s; display: inline-block; }
        .btn-gold { background: var(--neon-gold); color: black; }
        .btn-gold:hover { background: #fff; box-shadow: 0 0 10px var(--neon-gold); }
        .btn-danger { background: #d32f2f; color: white; }
        .btn-green { background: #2e7d32; color: white; }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
        .modal-content { background: #181818; margin: 5% auto; padding: 30px; border: 1px solid var(--neon-gold); width: 90%; max-width: 600px; border-radius: 10px; position: relative; max-height: 90vh; overflow-y: auto; }
        .close-modal { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: white; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; color: var(--neon-gold); margin-bottom: 5px; font-size: 0.9rem; }
        .form-group input, .form-group select { width: 100%; padding: 10px; background: #333; border: 1px solid #555; color: white; border-radius: 4px; }
        
        .security-info {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 215, 0, 0.1);
            border-left: 3px solid var(--neon-gold);
            border-radius: 5px;
            font-size: 0.85rem;
            color: #ccc;
        }

        /* Year-wise boxes grid */
        .yearwise-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .year-box {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .year-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
            border-color: var(--neon-gold);
        }
        .year-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--neon-gold);
            opacity: 0.7;
        }
        .year-label {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--neon-gold);
            margin-bottom: 5px;
        }
        .year-count {
            font-size: 2.2rem;
            font-weight: bold;
            color: white;
            margin: 5px 0;
        }
        .year-text {
            font-size: 0.8rem;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .year-percentage {
            font-size: 0.75rem;
            color: #888;
            margin-top: 5px;
        }

        /* Export section */
        .export-section {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            margin: 40px 0;
            border: 1px solid var(--border);
        }
        .export-section h3 {
            color: var(--neon-gold);
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .export-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .charts-grid { grid-template-columns: 1fr; }
            .yearwise-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
            .export-buttons { flex-direction: column; }
        }
    </style>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <div class="login-wrap">
        <div class="login-box">
            <h2 style="color:var(--neon-gold);">ADMIN ACCESS</h2>
            <?php if(isset($error_msg)) echo "<p style='color:red'>$error_msg</p>"; ?>
            <?php if(isset($_GET['session_expired']) && $_GET['session_expired'] == 1) echo "<p style='color:orange'>Your session has expired. Please login again.</p>"; ?>
            <form method="POST" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login_btn" class="btn btn-gold" style="width:100%; margin-top:10px;" <?php if ($is_locked_out) echo 'disabled'; ?>>LOGIN</button>
            </form>
            <div class="security-info">
                <p><i class="fas fa-shield-alt"></i> For security reasons, this admin panel is protected by:</p>
                <ul style="text-align: left; margin-top: 10px;">
                    <li>CSRF token protection</li>
                    <li>Brute-force attack prevention (3 attempts, 10 min lockout)</li>
                    <li>Session timeout after inactivity</li>
                    <li>⚠️ Plain-text password (for development only)</li>
                </ul>
            </div>
        </div>
    </div>
<?php else: ?>

    <nav class="navbar">
        <div class="brand"><i class="fas fa-chart-line"></i> REBOOT 40 ADMIN</div>
        <a href="?action=logout" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <div class="container">
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($stats['total_attendees']); ?></h3>
                    <p>Total Headcount</p>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($stats['total_alumni']); ?></h3>
                    <p>Alumni Registered</p>
                </div>
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($stats['total_family']); ?></h3>
                    <p>Family Members</p>
                </div>
                <div class="stat-icon"><i class="fas fa-child"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($mem_count); ?></h3>
                    <p>Memories Shared</p>
                </div>
                <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
            </div>
        </div>

        <!-- Year-wise Registration Boxes -->
        <div class="section-header">
            <h2 style="color:var(--neon-gold);"><i class="fas fa-calendar"></i> Year-wise Alumni Registration</h2>
        </div>
        
        <div class="yearwise-grid">
            <?php
            if (!empty($all_years)) {
                $total_alumni = $stats['total_alumni'];
                foreach ($all_years as $year_data) {
                    $percentage = $total_alumni > 0 ? round(($year_data['count'] / $total_alumni) * 100, 1) : 0;
                    echo '
                    <div class="year-box">
                        <div class="year-label">' . htmlspecialchars($year_data['year']) . '</div>
                        <div class="year-count">' . htmlspecialchars($year_data['count']) . '</div>
                        <div class="year-text">Alumni</div>
                        <div class="year-percentage">' . $percentage . '% of total</div>
                    </div>';
                }
            } else {
                echo '<p style="color:#888; text-align:center; grid-column:1/-1;">No alumni data available</p>';
            }
            ?>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h3><i class="fas fa-calendar-alt"></i> Registration Timeline (Day Wise)</h3>
                <canvas id="dateChart"></canvas>
            </div>
            <div class="chart-container">
                <h3><i class="fas fa-graduation-cap"></i> Alumni by Graduation Year</h3>
                <canvas id="yearChart"></canvas>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <h3><i class="fas fa-file-export"></i> Export Data</h3>
            <div class="export-buttons">
                <a href="?export=alumni&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="btn btn-green">
                    <i class="fas fa-file-excel"></i> Export All Alumni Data (CSV)
                </a>
                <a href="?export=memory_notes&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="btn btn-green">
                    <i class="fas fa-file-excel"></i> Export Memory Wall Data (CSV)
                </a>
                <a href="?export=yearwise_stats&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="btn btn-gold">
                    <i class="fas fa-chart-bar"></i> Export Year-wise Statistics (CSV)
                </a>
            </div>
        </div>

        <div class="section-header">
            <h2 style="color:var(--neon-gold);">Alumni Database</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Contact</th>
                        <th>Family</th>
                        <th>Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM alumni ORDER BY id DESC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        $json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td style='color:white; font-weight:bold;'>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['graduation_year']) . "</td>
                            <td>" . htmlspecialchars($row['phone']) . "</td>
                            <td><span class='badge'>" . htmlspecialchars($row['participating_with_family']) . "</span></td>
                            <td>" . htmlspecialchars($row['family_count']) . "</td>
                            <td>
                                <button onclick='openEdit($json)' class='btn btn-gold btn-sm'><i class='fas fa-edit'></i></button>
                                <a href='?delete=true&type=alumni&id=" . htmlspecialchars($row['id']) . "&csrf_token=" . $_SESSION['csrf_token'] . "' onclick='return confirm(\"Confirm delete?\")' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="section-header">
            <h2 style="color:var(--neon-gold);">Memory Wall</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM memory_notes ORDER BY created_at DESC");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td style='max-width:400px;'>" . htmlspecialchars($row['message']) . "</td>
                            <td>" . htmlspecialchars($row['created_at']) . "</td>
                            <td><a href='?delete=true&type=memory&id=" . htmlspecialchars($row['id']) . "&csrf_token=" . $_SESSION['csrf_token'] . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
            <h3 style="color:var(--neon-gold); border-bottom:1px solid #333; padding-bottom:10px; margin-top:0;">Edit Alumni</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="e_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="e_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="e_email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" id="e_phone" required>
                    </div>
                    <div class="form-group">
                        <label>Grad Year</label>
                        <input type="number" name="graduation_year" id="e_year" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="current_company" id="e_company">
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" name="current_position" id="e_position">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>With Family?</label>
                        <select name="participating_with_family" id="e_family">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Family Count</label>
                        <input type="number" name="family_count" id="e_count" min="0">
                    </div>
                </div>
                <div style="text-align:right; margin-top:20px;">
                    <button type="submit" name="update_alumni" class="btn btn-gold">Update Record</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const yearLabels = <?php echo json_encode($year_labels); ?>;
        const yearData = <?php echo json_encode($year_data); ?>;
        const dateLabels = <?php echo json_encode($date_labels); ?>;
        const dateData = <?php echo json_encode($date_data); ?>;

        Chart.defaults.color = '#888';
        Chart.defaults.borderColor = '#333';

        new Chart(document.getElementById('yearChart'), {
            type: 'bar',
            data: {
                labels: yearLabels,
                datasets: [{
                    label: 'Alumni Count',
                    data: yearData,
                    backgroundColor: '#FFD700',
                    borderColor: '#FFD700',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('dateChart'), {
            type: 'line',
            data: {
                labels: dateLabels,
                datasets: [{
                    label: 'Registrations per Day',
                    data: dateData,
                    borderColor: '#00d2ff',
                    backgroundColor: 'rgba(0, 210, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        function openEdit(data) {
            document.getElementById('e_id').value = data.id;
            document.getElementById('e_name').value = data.name;
            document.getElementById('e_email').value = data.email;
            document.getElementById('e_phone').value = data.phone;
            document.getElementById('e_year').value = data.graduation_year;
            document.getElementById('e_company').value = data.current_company;
            document.getElementById('e_position').value = data.current_position;
            document.getElementById('e_family').value = data.participating_with_family;
            document.getElementById('e_count').value = data.family_count;
            document.getElementById('editModal').style.display = 'block';
        }

        window.onclick = function(e) {
            if (e.target == document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = 'none';
            }
        }
    </script>
<?php endif; ?>

</body>
</html>