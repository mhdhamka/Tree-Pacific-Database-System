<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php
$count = 0;

if(isset($_POST['addUser'])) {
    $realname = $_POST['realname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usertype = $_POST['usertype'];

    global $conn;
    $sql = "SELECT * FROM user";
    $result = mysqli_query($conn, $sql);

    if ($result && $result->num_rows > 0)
    {
        while ($row = $result->fetch_assoc())
        {
            $count = $row['UserID'];
            $count++;
        }
    }
    
    $sql = "INSERT INTO user(UserID, Username, Email, PasswordHash, RealName, UserType)
            VALUES('$count', '$username', '$email', '$password', '$realname', '$usertype')";
            
    if (mysqli_query($conn, $sql))
    {   
        $success = true;
    }
    else
    {
        $error_message = mysqli_error($conn);
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Add User</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include(__DIR__ . '../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumbs -->
                <ul class="breadcrumb">
                    <li><a href="../staff/dashboard.php">Accounts</a></li>
                    <li class="separator"><i class="fa fa-angle-right"></i></li>
                    <li class="active">Add User</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Register New Account</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success) && $success): ?>
                            <div class="alert alert-success">User added successfully!</div>
                        <?php elseif (isset($error_message)): ?>
                            <div class="alert alert-danger">Error: <?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="realname">Real Name</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-id-card"></i>
                                    <input type="text" class="form-control-input" name="realname" id="realname" placeholder="Enter full name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control-input" name="username" id="username" placeholder="Enter username" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control-input" name="email" id="email" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-lock"></i>
                                    <input type="password" class="form-control-input" name="password" id="password" placeholder="Enter password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>User Role</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="C" required checked> Client
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="usertype" value="S" required> Staff
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="addUser" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i> Create User
                                </button>
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