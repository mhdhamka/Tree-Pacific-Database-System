<?php
// Include the database connection & session handler
include(__DIR__ . '/../../config/dbConnect.php');

$error_msg   = "";
$success_msg = "";

/* ==========================================================================
   1. ACTION: STAFF LOGIN (UserType = 'S')
   ========================================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'login_staff') {
    $userid   = trim($_POST['userid'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($userid) && !empty($password)) {
        // Authenticate via UserID, Username, or Email for Staff ('S')
        $stmt = $conn->prepare("SELECT UserID, Username, Email, RealName, PasswordHash, UserType 
                                FROM user 
                                WHERE (UserID = ? OR Username = ? OR Email = ?) 
                                  AND UserType = 'S' 
                                LIMIT 1");
        $stmt->bind_param("sss", $userid, $userid, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['PasswordHash']) || $password === $row['PasswordHash']) {
                
                // Store session variables
                $_SESSION['UserID']   = $row['UserID'];
                $_SESSION['Username'] = $row['Username'];
                $_SESSION['Email']    = $row['Email'];
                $_SESSION['RealName'] = $row['RealName'];
                $_SESSION['UserType'] = 'S'; 

                // Update LogStatus = 1
                $updateStmt = $conn->prepare("UPDATE user SET LogStatus = 1 WHERE UserID = ?");
                $updateStmt->bind_param("s", $row['UserID']);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect to Staff Dashboard via BASE_URL
                header("Location: " . BASE_URL . "/src/views/staff/dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid Staff ID or Password.";
            }
        } else {
            $error_msg = "Invalid Staff ID or Password.";
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill in all fields.";
    }
}


/* ==========================================================================
   2. ACTION: CLIENT LOGIN (UserType = 'C')
   ========================================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'login_client') {
    $userid   = trim($_POST['userid'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($userid) && !empty($password)) {
        // Authenticate via UserID, Username, or Email for Client ('C')
        $stmt = $conn->prepare("SELECT UserID, Username, Email, RealName, PasswordHash, UserType 
                                FROM user 
                                WHERE (UserID = ? OR Username = ? OR Email = ?) 
                                  AND UserType = 'C' 
                                LIMIT 1");
        $stmt->bind_param("sss", $userid, $userid, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['PasswordHash']) || $password === $row['PasswordHash']) {
                
                // Store session variables
                $_SESSION['UserID']   = $row['UserID'];
                $_SESSION['Username'] = $row['Username'];
                $_SESSION['Email']    = $row['Email'];
                $_SESSION['RealName'] = $row['RealName'];
                $_SESSION['UserType'] = 'C'; 

                // Update LogStatus = 1
                $updateStmt = $conn->prepare("UPDATE user SET LogStatus = 1 WHERE UserID = ?");
                $updateStmt->bind_param("s", $row['UserID']);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect to Client Dashboard via BASE_URL
                header("Location: " . BASE_URL . "/src/views/client/dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid User ID or Password.";
            }
        } else {
            $error_msg = "Invalid User ID or Password.";
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill in all fields.";
    }
}


/* ==========================================================================
   3. ACTION: CLIENT REGISTRATION (UserType = 'C')
   ========================================================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'register_client') {
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $realName  = trim($_POST['realName'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');

    if (!empty($username) && !empty($email) && !empty($realName) && !empty($password) && !empty($phone)) {
        
        $checkStmt = $conn->prepare("SELECT UserID FROM user WHERE Username = ? OR Email = ? LIMIT 1");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();

        if ($checkRes->num_rows > 0) {
            $error_msg = "Username or Email is already taken!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $userType  = 'C';
            $logStatus = 0;

            // 8 Columns total
            $insertStmt = $conn->prepare("INSERT INTO user (Username, Email, PasswordHash, RealName, Phone, Address, UserType, LogStatus) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            // 7 Strings ('s') + 1 Integer ('i') = "sssssssi"
            $insertStmt->bind_param("sssssssi", $username, $email, $hashedPassword, $realName, $phone, $address, $userType, $logStatus);

            if ($insertStmt->execute()) {
                $success_msg = "Registration successful! You can now log in.";
                header("Location: ../../views/public/LoginClient.php?success=" . urlencode($success_msg));
                exit();
            } else {
                $error_msg = "Registration failed. Please try again later.";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    } else {
        $error_msg = "Please fill in all required registration fields.";
    }
}
?>