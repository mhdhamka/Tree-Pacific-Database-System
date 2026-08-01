
<?php
include(__DIR__ . '../../config/dbConnect.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Userid = trim($_POST['userid'] ?? '');
    $Password = trim($_POST['password'] ?? '');

    if (!empty($Userid) && !empty($Password)) {
        // Prepared statement to prevent SQL Injection
        $stmt = mysqli_prepare($conn, "SELECT UserID, PasswordHash FROM user WHERE UserID = ? AND UserType = 'S' LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $Userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password hash (fallback to exact match for legacy plaintext)
            $passwordValid = password_verify($Password, $row['PasswordHash']) || ($Password === $row['PasswordHash']);

            if ($passwordValid) {
                $_SESSION['login_user'] = $row['UserID'];

                // Update user login status
                $updateStmt = mysqli_prepare($conn, "UPDATE user SET LogStatus = 1 WHERE UserID = ?");
                mysqli_stmt_bind_param($updateStmt, "s", $row['UserID']);
                mysqli_stmt_execute($updateStmt);

                header("Location: ../staff/dashboard.php");
                exit();
            }
        }

        // Redirect on invalid login
        header("Location: LoginStaff.php?remark_login=failed");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Staff Login</title>
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/loginStaff.css">

</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <div class="brand">
            <img src="../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1>Pacific<span>Tree</span></h1>
        </div>
        <div class="nav-badge">Staff Portal</div>
    </nav>

    <!-- Main Content Grid -->
    <main class="main-wrapper">
        <div class="login-card">
            
            <!-- Left Hero Section -->
            <section class="card-hero">
                <img src="../../assets/images/TREE.PNG" alt="PacificTree Logo">
                <h2>PacificTree</h2>
                <p>Tree Profiling Management System</p>
            </section>

            <!-- Right Form Section -->
            <section class="card-form">
                <div class="form-header">
                    <h3><span>Staff</span> Log In</h3>
                    <p>Enter your credentials to access your account</p>
                </div>

                <?php if (isset($_GET['remark_login']) && $_GET['remark_login'] === 'failed'): ?>
                    <div class="alert-error">
                        Invalid Staff ID or Password. Please try again.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label for="userid">Staff ID</label>
                        <input type="text" placeholder="e.g. STF-102" id="userid" name="userid" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" placeholder="••••••••" id="password" name="password" required>
                    </div>

                    <button type="submit" name="submit" class="btn-submit">LOG IN</button>
                </form>

                <a href="OptionLogin.php" class="btn-switch">Change User Type</a>

                <div class="form-footer">
                    New to PacificTree? <a href="SignUpStaff.php">Sign Up</a>
                </div>
            </section>

        </div>
    </main>

    <!-- Page Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> PACIFICTREE. All rights reserved.</p>
    </footer>

</body>
</html>