<?php

class ProfileController
{
    private $conn;
    private $userId;
    private $flashMessage = '';
    private $flashType = '';
    private $userData = [];

    public function __construct($dbConnection)
    {
        // 1. Session check
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->conn = $dbConnection;

        // 2. Authentication Guard / User ID Setup
        if (!isset($_SESSION['UserID']) && !isset($_SESSION['user_id'])) {
            $this->userId = 1; // Fallback demo ID
        } else {
            $this->userId = $_SESSION['UserID'] ?? $_SESSION['user_id'];
        }

        // Initialize default user data
        $this->userData = [
            'RealName'    => 'Valued Client',
            'Username'    => 'client_user',
            'Email'       => 'client@example.com',
            'Phone'       => 'Not provided',
            'Address'     => 'Not provided',
            'Latitude'    => 1.5533,  // Default: Kuching / SWK baseline
            'Longitude'   => 110.3592,
            'MemberSince' => date('Y-m-d')
        ];
    }

    /**
     * Main handler to process requests and fetch data.
     */
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostActions();
        }

        $this->fetchUserData();
    }

    /**
     * Route POST actions to their respective handlers.
     */
    private function handlePostActions()
    {
        if (!$this->conn) {
            return;
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'update_profile':
                $this->updateProfile();
                break;
            case 'change_password':
                $this->changePassword();
                break;
        }
    }

    /**
     * Action A: Update Profile Details (Including Geolocation)
     */
    private function updateProfile()
    {
        $realName  = trim($_POST['real_name'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $address   = trim($_POST['address'] ?? '');
        $latitude  = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);

        // Check if Lat/Lng columns exist in database table
        $hasLat = $this->conn->query("SHOW COLUMNS FROM user LIKE 'Latitude'")->num_rows > 0;

        if ($hasLat) {
            $updateQuery = "UPDATE user SET RealName = ?, Phone = ?, Address = ?, Latitude = ?, Longitude = ? WHERE UserID = ?";
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("sssddi", $realName, $phone, $address, $latitude, $longitude, $this->userId);
        } else {
            $updateQuery = "UPDATE user SET RealName = ?, Phone = ?, Address = ? WHERE UserID = ?";
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("sssi", $realName, $phone, $address, $this->userId);
        }
        
        if ($stmt) {
            if ($stmt->execute()) {
                $_SESSION['RealName'] = $realName;
                $this->setFlash("Profile details and location updated successfully!", "success");
            } else {
                $this->setFlash("Failed to update profile. Please try again.", "error");
            }
            $stmt->close();
        }
    }

    /**
     * Action B: Change User Password
     */
    private function changePassword()
    {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $this->setFlash("New passwords do not match.", "error");
            return;
        }

        // Validate Current Password
        $pwdQuery = "SELECT Password FROM user WHERE UserID = ?";
        if ($stmt = $this->conn->prepare($pwdQuery)) {
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                if ($currentPassword === $row['Password'] || password_verify($currentPassword, $row['Password'])) {
                    $hashedPwd = password_hash($newPassword, PASSWORD_BCRYPT);
                    $updatePwd = "UPDATE user SET Password = ? WHERE UserID = ?";

                    if ($stmtUp = $this->conn->prepare($updatePwd)) {
                        $stmtUp->bind_param("si", $hashedPwd, $this->userId);
                        $stmtUp->execute();
                        $stmtUp->close();
                        
                        $this->setFlash("Password changed successfully!", "success");
                    }
                } else {
                    $this->setFlash("Incorrect current password.", "error");
                }
            }
            $stmt->close();
        }
    }

    /**
     * Fetch Fresh Profile Data safely
     */
    private function fetchUserData()
    {
        if (!$this->conn) {
            return;
        }

        $hasPhone   = $this->conn->query("SHOW COLUMNS FROM user LIKE 'Phone'")->num_rows > 0;
        $hasAddress = $this->conn->query("SHOW COLUMNS FROM user LIKE 'Address'")->num_rows > 0;
        $hasLat     = $this->conn->query("SHOW COLUMNS FROM user LIKE 'Latitude'")->num_rows > 0;

        $selectCols = "RealName, Username, Email";
        if ($hasPhone)   $selectCols .= ", Phone";
        if ($hasAddress) $selectCols .= ", Address";
        if ($hasLat)     $selectCols .= ", Latitude, Longitude";

        $userQuery = "SELECT {$selectCols} FROM user WHERE UserID = ?";
        if ($stmt = $this->conn->prepare($userQuery)) {
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $this->userData['RealName']  = $row['RealName'] ?? $this->userData['RealName'];
                $this->userData['Username']  = $row['Username'] ?? $this->userData['Username'];
                $this->userData['Email']     = $row['Email'] ?? $this->userData['Email'];
                $this->userData['Phone']     = $row['Phone'] ?? 'Not set';
                $this->userData['Address']   = $row['Address'] ?? 'Not set';
                $this->userData['Latitude']  = $row['Latitude'] ?? 1.5533;
                $this->userData['Longitude'] = $row['Longitude'] ?? 110.3592;
            }
            $stmt->close();
        }
    }

    private function setFlash($message, $type)
    {
        $this->flashMessage = $message;
        $this->flashType    = $type;
    }

    // Getters
    public function getUserData() { return $this->userData; }
    public function getFlashMessage() { return $this->flashMessage; }
    public function getFlashType() { return $this->flashType; }
}