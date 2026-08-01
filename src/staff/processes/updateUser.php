<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php
    global $conn;
    $updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;

    // Handle Form Submission
    if (isset($_POST['updateUser'])) {   
        $realname = mysqli_real_escape_string($conn, $_POST['realname']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);   
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);
        
        $sql = "UPDATE user SET RealName = '$realname', Username = '$username', Email = '$email', PasswordHash = '$password', UserType = '$usertype' WHERE UserID = $updateID";
        $result = mysqli_query($conn, $sql);
        header("Location: dashboard.php");
        exit();
    }

    // Fetch Logged-in Staff Username
    $staffQuery = "SELECT Username FROM user WHERE logStatus = 1 AND UserType = 'S' LIMIT 1";
    $staffResult = mysqli_query($conn, $staffQuery);
    $staffName = ($staffResult && $row = mysqli_fetch_assoc($staffResult)) ? $row['Username'] : 'Staff';

    // Fetch Target User Data to Pre-fill Form
    $userQuery = "SELECT * FROM user WHERE UserID = $updateID LIMIT 1";
    $userResult = mysqli_query($conn, $userQuery);
    $userData = mysqli_fetch_assoc($userResult);

    $realNameVal = $userData['RealName'] ?? '';
    $usernameVal = $userData['Username'] ?? '';
    $emailVal    = $userData['Email'] ?? '';
    $passVal     = $userData['PasswordHash'] ?? '';
    $userTypeVal = $userData['UserType'] ?? 'C';
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update User</title>
    
    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <ul class="breadcrumb">
                    <li><a href="../dashboard.php">Users</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Update User</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update User Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>User ID</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-id-badge"></i>
                                    <input type="text" class="form-control-input" value="#<?php echo htmlspecialchars($updateID); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="realname">Real Name</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-id-card"></i>
                                    <input type="text" class="form-control-input" name="realname" id="realname" value="<?php echo htmlspecialchars($realNameVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" class="form-control-input" name="username" id="username" value="<?php echo htmlspecialchars($usernameVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" class="form-control-input" name="email" id="email" value="<?php echo htmlspecialchars($emailVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password Hash</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" class="form-control-input" name="password" id="password" value="<?php echo htmlspecialchars($passVal); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>User Type</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="C" <?php echo ($userTypeVal === 'C') ? 'checked' : ''; ?>>
                                        Client
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="S" <?php echo ($userTypeVal === 'S') ? 'checked' : ''; ?>>
                                        Staff
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="updateUser" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Save Changes
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-xmark"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../../includes/footer.php'); ?>
        </main>
    </div>
</body>
</html>