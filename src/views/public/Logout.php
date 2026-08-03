<?php
include(__DIR__ . '/../../config/dbConnect.php');

$userType = $_SESSION['UserType'] ?? null;

// Logout user
if (isset($_SESSION['UserID'])) {
    $updateStmt = $conn->prepare("UPDATE user SET LogStatus = 0 WHERE UserID = ?");
    $updateStmt->bind_param("s", $_SESSION['UserID']);
    $updateStmt->execute();
    $updateStmt->close();
}

// Clear and destroy session
$_SESSION = array();
session_destroy();

// Redirect back based on user type using BASE_URL
if ($userType === 'S') {
    header("Location: " . BASE_URL . "/src/views/public/LoginStaff.php");
} elseif ($userType === 'C') {
    header("Location: " . BASE_URL . "/src/views/public/LoginClient.php");
} else {
    header("Location: " . BASE_URL . "/src/views/public/OptionLogin.php");
}
exit();
?>