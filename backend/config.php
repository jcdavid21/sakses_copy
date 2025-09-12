<?php
/**
 * SAKSES Database Configuration
 * Smart Analytics for Knowledge-driven Success Evaluation System
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sakses_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Python Flask API configuration
define('PYTHON_API_URL', 'http://localhost:8800');

// Application settings
define('APP_NAME', 'SAKSES');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Smart Analytics for Knowledge-driven Success Evaluation System');

// Session configuration
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);

// Function to call Python ML API
function callMLAPI($endpoint, $data = null) {
    $url = PYTHON_API_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        error_log("CURL Error: " . $error);
        return null;
    }
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    
    error_log("ML API Error - HTTP Code: " . $http_code . ", Response: " . $response);
    return null;
}

// Function to log system activities
function logActivity($user_id, $action, $module, $description = '') {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, module, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $action, $module, $description, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to check user role
function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_role = $_SESSION['user_role'] ?? '';
    
    $role_hierarchy = [
        'admin' => 4,
        'supervisor' => 3,
        'analyst' => 2,
        'field_worker' => 1
    ];
    
    $user_level = $role_hierarchy[$user_role] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;
    
    return $user_level >= $required_level;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to generate secure password hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}


// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('Asia/Manila');
?>