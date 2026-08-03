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
    // 1. ADD OPERATIONS
    // ==========================================
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $realname = trim($_POST['realname'] ?? '');
                $username = trim($_POST['username'] ?? '');
                $email    = trim($_POST['email'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $phone    = trim($_POST['phone'] ?? '');
                $address  = trim($_POST['address'] ?? '');
                $usertype = trim($_POST['usertype'] ?? 'C');

                $stmt = $conn->prepare("INSERT INTO user (Username, Email, PasswordHash, RealName, Phone, Address, UserType) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $username, $email, $password, $realname, $phone, $address, $usertype);
                
                if ($stmt->execute()) {
                    header("Location: ../../views/staff/dashboard.php?msg=added");
                } else {
                    header("Location: ../../views/staff/dashboard.php?error=" . urlencode($stmt->error));
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'addStaff':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userID     = intval($_POST['userID']);
                $employdate = mysqli_real_escape_string($conn, $_POST['employdate']);
                $salary     = mysqli_real_escape_string($conn, $_POST['salary']);
                $IsManager  = mysqli_real_escape_string($conn, $_POST['IsManager']);

                // Check 1: Ensure User ID exists in parent `user` table
                $checkUser = mysqli_query($conn, "SELECT UserID FROM user WHERE UserID = $userID");
                if (mysqli_num_rows($checkUser) === 0) {
                    header("Location: ../../views/staff/dashboard.php?error=" . urlencode("User ID $userID does not exist. Please create the user first."));
                    exit();
                }

                // Check 2: Ensure User ID is not already registered as staff
                $checkStaff = mysqli_query($conn, "SELECT UserID FROM staff WHERE UserID = $userID");
                if (mysqli_num_rows($checkStaff) > 0) {
                    header("Location: ../../views/staff/dashboard.php?error=" . urlencode("User ID $userID is already registered as a staff member."));
                    exit();
                }

                $sql = "INSERT INTO staff (UserID, EmployDate, Salary, IsManager) 
                        VALUES ('$userID', '$employdate', '$salary', '$IsManager')";

                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=staff_added");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'addClient':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userID      = intval($_POST['userID']);
                $companyName = mysqli_real_escape_string($conn, $_POST['companyName']);
                $address     = mysqli_real_escape_string($conn, $_POST['address']);
                $country     = mysqli_real_escape_string($conn, $_POST['country']);

                // Ensure User ID exists in parent `user` table
                $checkUser = mysqli_query($conn, "SELECT UserID FROM user WHERE UserID = $userID");
                if (mysqli_num_rows($checkUser) === 0) {
                    header("Location: ../../views/staff/dashboard.php?error=" . urlencode("User ID $userID does not exist. Please create the user first."));
                    exit();
                }

                // Insert into client table with Address and Country
                $sql = "INSERT INTO client (UserID, Address, Country) 
                        VALUES ('$userID', '$address', '$country')";

                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=client_added");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;


    // ==========================================
    // 2. UPDATE OPERATIONS (AJAX & Interactive)
    // ==========================================
    case 'toggleManager':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $staffID   = intval($_POST['staffID'] ?? 0);
            $isManager = intval($_POST['isManager'] ?? 0);

            if ($staffID > 0) {
                $stmt = $conn->prepare("UPDATE staff SET IsManager = ? WHERE UserID = ?");
                $stmt->bind_param("ii", $isManager, $staffID);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid staff ID']);
            }
            exit();
        }
        break;

    case 'updateField':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $staffID = intval($_POST['staffID'] ?? 0);
            $field   = $_POST['field'] ?? '';
            $value   = $_POST['value'] ?? '';

            // Whitelist allowed columns to prevent unauthorized field updates
            $allowedFields = ['Salary', 'EmployDate'];

            if ($staffID > 0 && in_array($field, $allowedFields)) {
                $stmt = $conn->prepare("UPDATE staff SET `$field` = ? WHERE UserID = ?");
                $stmt->bind_param("si", $value, $staffID);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid field or staff ID']);
            }
            exit();
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $updateID = intval($_POST['userID']);
                $realname = trim($_POST['realname'] ?? '');
                $username = trim($_POST['username'] ?? '');
                $email    = trim($_POST['email'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $phone    = trim($_POST['phone'] ?? '');
                $address  = trim($_POST['address'] ?? '');
                $usertype = trim($_POST['usertype'] ?? 'C');

                $stmt = $conn->prepare("UPDATE user 
                                        SET RealName = ?, Username = ?, Email = ?, Phone = ?, Address = ?, PasswordHash = ?, UserType = ? 
                                        WHERE UserID = ?");
                $stmt->bind_param("sssssssi", $realname, $username, $email, $phone, $address, $password, $usertype, $updateID);

                if ($stmt->execute()) {
                    header("Location: ../../views/staff/dashboard.php?msg=updated");
                } else {
                    header("Location: ../../views/staff/dashboard.php?error=" . urlencode($stmt->error));
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'updateClient':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $updateID = intval($_POST['userID'] ?? $_GET['updateID'] ?? 0);
                $address  = mysqli_real_escape_string($conn, $_POST['address']);
                $country  = mysqli_real_escape_string($conn, $_POST['country']);
                $photo    = mysqli_real_escape_string($conn, $_POST['photo']);

                $sql = "UPDATE client 
                        SET Address = '$address', Country = '$country', Photo = '$photo' 
                        WHERE UserID = $updateID";

                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=client_updated");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'updateClientAddress':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $cID     = intval($_POST['cID'] ?? 0);
            $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
            $country = mysqli_real_escape_string($conn, $_POST['country'] ?? '');

            if ($cID > 0) {
                $sql = "UPDATE client SET Address = '$address', Country = '$country' WHERE UserID = $cID";

                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid client ID']);
            }
            exit();
        }
        break;

    // ==========================================
    // 3. DELETE OPERATIONS
    // ==========================================
    case 'delete':
        $deleteID = isset($_GET['deleteID']) ? intval($_GET['deleteID']) : 0;

        if ($deleteID > 0) {
            try {
                $sql = "DELETE FROM user WHERE UserID = $deleteID";
                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=deleted");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'deleteStaff':
        $deleteID = isset($_GET['deleteID']) ? intval($_GET['deleteID']) : 0;

        if ($deleteID > 0) {
            try {
                $sql = "DELETE FROM staff WHERE UserID = $deleteID";
                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=staff_deleted");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    case 'deleteClient':
        $deleteID = isset($_GET['deleteID']) ? intval($_GET['deleteID']) : 0;

        if ($deleteID > 0) {
            try {
                $sql = "DELETE FROM client WHERE UserID = $deleteID";
                mysqli_query($conn, $sql);
                header("Location: ../../views/staff/dashboard.php?msg=client_deleted");
            } catch (mysqli_sql_exception $e) {
                header("Location: ../../views/staff/dashboard.php?error=" . urlencode($e->getMessage()));
            }
            exit();
        }
        break;

    default:
        header("Location: ../../views/staff/dashboard.php");
        exit();
}