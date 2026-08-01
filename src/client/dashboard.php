<?php 

include (__DIR__ . '../../config/dbConnect.php');

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree - Client Dashboard</title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../assets/css/client.css">
        
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="brand">
            <img src="../../assets/images/TREE.PNG" alt="PacificTree Logo">
            <h1 class="brand-title">PacificTree</h1>
        </div>
        <span class="page-badge">Client Dashboard</span>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul>
            <li><a class="active" href="MenuClient.php">Homepage</a></li>
            <li><a href="ProfileClient.php">Profile</a></li>
            <li><a href="PurchaseClient.php">Purchase</a></li>
            <li class="logout-item"><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <!-- Main Content Grid -->
    <main class="main-container">
        
        <!-- System Hero Graphic -->
        <div class="hero-card">
            <img src="../../assets/images/TREE.PNG" alt="PacificTree Graphic">
            <h2>PacificTree</h2>
            <p>Tree Profiling Management System</p>
        </div>

        <!-- Information / About Box -->
        <article class="info-card">
            <div class="card-header">
                <h3><span>PacificTree</span> &mdash; About</h3>
            </div>
            <div class="quote-content">
                It costs 38 trillion dollars to produce oxygen for all humans on the planet for six months. This indicates that even if we spent all of the money in the world, we would be unable to provide oxygen for all humans for six months. Trees do it for free.
            </div>
        </article>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> PACIFICTREE. ALL RIGHTS RESERVED.</p>
    </footer>

</body>
</html>