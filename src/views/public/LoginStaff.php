<?php
include(__DIR__ . '/../../config/dbConnect.php');
include(__DIR__ . '/../../controllers/auth/authController.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Staff Login</title>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/loginStaff.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <div class="brand">
            <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1>Pacific<span>Tree</span></h1>
        </div>
        <div class="nav-badge">Staff Portal</div>
    </nav>

    <!-- Main Content Grid -->
    <main class="main-wrapper">
        <div class="login-card">
            
            <!-- Left Hero Section -->
            <section class="card-hero">
                <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
                <h2>PacificTree</h2>
                <p>Enterprise GIS & Forestry Operations Platform</p>
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

                <form method="POST" action="../../controllers/auth/authController.php">
                    <!-- ADDED: Hidden field required by authController.php -->
                    <input type="hidden" name="action" value="login_staff">

                    <div class="form-group">
                        <label for="userid">Staff ID / Username / Email</label>
                        <input type="text" placeholder="e.g. STF-102" id="userid" name="userid" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" placeholder="••••••••" id="password" name="password" required>
                    </div>

                    <button type="submit" name="submit" class="btn-submit">LOG IN</button>
                </form>

                <a href="OptionLogin.php" class="btn-switch">
                    <i class="fa-solid fa-arrows-rotate"></i> Change User Type
                </a>

            </section>

        </div>
    </main>

    <!-- Footer -->
    <?php include(__DIR__ . '/../../includes/footerClient.php'); ?>

</body>
</html>