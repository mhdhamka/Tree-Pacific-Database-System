<?php

include(__DIR__ . '/../../config/dbConnect.php');
global $conn;

if (!$conn) {
    die("Database connection error");
}

// Determine action based on request
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ==========================================
    // 1. ADD USER
    // ==========================================
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $realname = mysqli_real_escape_string($conn, $_POST['realname']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email    = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);

            $sql = "INSERT INTO user (Username, Email, PasswordHash, RealName, UserType)
                    VALUES ('$username', '$email', '$password', '$realname', '$usertype')";

            if (mysqli_query($conn, $sql)) {
                header("Location: ../../views/staff/dashboard.php?msg=added");
            } else {
                header("Location: ../../staff/processes/addUser.php?error=" . urlencode(mysqli_error($conn)));
            }
            exit();
        }
        break;

    // ==========================================
    // 2. UPDATE USER
    // ==========================================
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateID = intval($_POST['userID']);
            $realname = mysqli_real_escape_string($conn, $_POST['realname']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $email    = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);

            $sql = "UPDATE user 
                    SET RealName = '$realname', Username = '$username', Email = '$email', PasswordHash = '$password', UserType = '$usertype' 
                    WHERE UserID = $updateID";

            if (mysqli_query($conn, $sql)) {
                header("Location: ../../views/staff/dashboard.php?msg=updated");
            } else {
                header("Location: ../../staff/processes/updateUser.php?updateID=$updateID&error=" . urlencode(mysqli_error($conn)));
            }
            exit();
        }
        break;

    // ==========================================
    // 3. DELETE USER
    // ==========================================
    case 'delete':
    $deleteID = isset($_GET['deleteID']) ? intval($_GET['deleteID']) : 0;
    
    if ($deleteID > 0) {
        $sql = "DELETE FROM user WHERE UserID = $deleteID";
        if (mysqli_query($conn, $sql)) {
            header("Location: ../../views/staff/dashboard.php?msg=deleted");
        } else {
            header("Location: ../../views/staff/dashboard.php?error=" . urlencode(mysqli_error($conn)));
        }
        exit();
    }
    break;

    default:
        header("Location: ../../views/staff/dashboard.php");
        exit();
}