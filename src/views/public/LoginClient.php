<?php
include(__DIR__ . '/../../config/dbConnect.php');
include(__DIR__ . '/../../controllers/auth/authController.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login | PacificTree</title>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <!-- Modern Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/loginClient.css">
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div class="brand">
            <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1>Tree<span>Pacific</span></h1>
        </div>
        <div class="nav-badge">
            Client Portal
        </div>
    </header>

    <!-- Main Container -->
    <main class="main-wrapper">
        <div class="login-card">
            
            <!-- Left Branding Side -->
            <section class="card-hero">
                <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
                <h2>PacificTree</h2>
                <p>Enterprise GIS & Forestry Operations Platform</p>
            </section>

            <!-- Right Login Form Side -->
            <section class="card-form">
                <div class="form-header">
                    <h3><span>Client</span> Log In</h3>
                    <p>Enter your credentials to access your client account</p>
                </div>

                <!-- Display Error Message if Authentication Fails -->
                <?php if (!empty($error_msg) || isset($_GET['remark_login'])): ?>
                    <div class="alert-error">
                        <?php echo !empty($error_msg) ? htmlspecialchars($error_msg) : "Login failed. Please try again."; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../../controllers/auth/authController.php">
                    <!-- ADDED: Hidden field required by authController.php -->
                    <input type="hidden" name="action" value="login_client">

                    <div class="form-group">
                        <label for="userid">User ID / Username / Email</label>
                        <input type="text" id="userid" name="userid" placeholder="e.g. C1001" required autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn-submit">LOG IN</button>
                </form>

                <a href="OptionLogin.php" class="btn-switch">
                    <i class="fa-solid fa-arrows-rotate"></i> Change User Type
                </a>

                <div class="form-footer">
                    New to PacificTree? <a href="register.php">Sign Up</a>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

</body>
</html>