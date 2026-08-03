<?php

    include(__DIR__ . '../../../config/dbConnect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Enterprise GIS & Forestry Operations Platform</title>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <!-- Modern Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../../assets/css/auth.css">

</head>

<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div class="brand">
            <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1>Pacific<span>Tree</span></h1>
        </div>
        <div class="nav-badge">
            System Portal
        </div>
    </header>

    <!-- Main Section -->
    <main class="main-wrapper">
        <div class="hero-container">
            
            <!-- Left Panel: Branding -->
            <section class="hero-brand-section">
                <img src="../../../assets/images/TREE.PNG" alt="PacificTree Logo">
                <h2>PacificTree</h2>
                <p>Enterprise GIS & Forestry Operations Platform</p>
            </section>

            <!-- Right Panel: Access Actions -->
            <section class="hero-login-section">
                <div class="login-header">
                    <h3>Welcome Back</h3>
                    <p>Select your account type to access the system</p>
                </div>

                <div class="action-buttons">
                    <a href="LoginClient.php" class="btn-portal btn-client">
                        Log In as Client
                    </a>
                    <a href="LoginStaff.php" class="btn-portal btn-staff">
                        Log In as Staff
                    </a>
                </div>

                <div class="footer-note">
                    Secure Database Portal 
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