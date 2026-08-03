<?php
// Always start the session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------------------------------
// DYNAMIC BASE URL DEFINITION
// Automatically calculates the project root path (works locally or in production)
// --------------------------------------------------------------------------
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    // Assumes dbConnect.php is located at /src/config/dbConnect.php (go up two levels to get root)
    $scriptDir = str_replace('\\', '/', dirname(__DIR__, 2)); 
    $docRoot   = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $appPath   = str_replace($docRoot, '', $scriptDir);
    
    define('BASE_URL', rtrim($protocol . $host . $appPath, '/'));
}

$hostName = "localhost";
$username = "root";
$password = "";
$database = "db_pt"; 

// Create connection
$conn = new mysqli($hostName, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current script filename and full relative script path
$currentPage = basename($_SERVER['PHP_SELF']);
$currentPath = strtolower($_SERVER['PHP_SELF']); 

// Define public UI pages
$publicPages = [
    'LoginClient.php', 
    'LoginStaff.php', 
    'register.php',
    'OptionLogin.php'
];

// Backend endpoints that don't enforce RBAC directly
$authEndpoints = [
    'authController.php',
    'logout.php'
];

// --------------------------------------------------------------------------
// 1. Prevent logged-in users from viewing login/signup forms
// --------------------------------------------------------------------------
if (in_array($currentPage, $publicPages)) {
    if (isset($_SESSION['UserID']) && isset($_SESSION['UserType'])) {
        if ($_SESSION['UserType'] === 'S') {
            header("Location: " . BASE_URL . "/src/views/staff/dashboard.php");
            exit();
        } elseif ($_SESSION['UserType'] === 'C') {
            header("Location: " . BASE_URL . "/src/views/client/dashboard.php");
            exit();
        }
    }
}

// --------------------------------------------------------------------------
// 2. Enforce login & Role-Based Access Control (RBAC)
// --------------------------------------------------------------------------
if (!in_array($currentPage, $publicPages) && !in_array($currentPage, $authEndpoints)) {
    
    // Check Authentication
    if (!isset($_SESSION['UserID'])) {
        if (strpos($currentPath, 'staff') !== false) {
            header("Location: " . BASE_URL . "/src/views/public/LoginStaff.php");
        } else {
            header("Location: " . BASE_URL . "/src/views/public/LoginClient.php");
        }
        exit();
    }

    // Role-Based Access Control Check
    $userType = $_SESSION['UserType'] ?? '';

    // Prevent Client ('C') from accessing Staff pages
    if ($userType === 'C' && strpos($currentPath, 'staff') !== false) {
        header("Location: " . BASE_URL . "/src/views/client/dashboard.php");
        exit();
    }

    // Prevent Staff ('S') from accessing Client pages
    if ($userType === 'S' && strpos($currentPath, 'client') !== false) {
        header("Location: " . BASE_URL . "/src/views/staff/dashboard.php");
        exit();
    }
}
?>