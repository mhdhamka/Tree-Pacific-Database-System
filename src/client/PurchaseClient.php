
<?php 
    // Fix 1: Properly include database connection & session handler
    include(__DIR__ . '/../config/dbConnect.php');
    
    // Include session management if available, otherwise safely define session variables
    if (file_exists(__DIR__ . '/../sessionClient.php')) {
        include(__DIR__ . '/../sessionClient.php');
    } else {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    global $conn;

    $loggedin_id = isset($_SESSION['ClientID']) ? $_SESSION['ClientID'] : (isset($loggedin_id) ? $loggedin_id : 0);
    $loggedin_session = isset($_SESSION['Username']) ? $_SESSION['Username'] : (isset($loggedin_session) ? $loggedin_session : 'Client Purchase');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Purchases</title>
    
    <!-- Modern Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">

    <style>
        :root {
            --primary: #128C7E;
            --primary-hover: #075E54;
            --accent: #04AA6D;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --radius: 16px;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* --- Header & Branding --- */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background-color: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 8px;
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.02em;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .user-info i {
            color: #4ade80;
            font-size: 1.1rem;
        }

        /* --- Navigation Bar --- */
        .navbar {
            background-color: #161f30;
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 0.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar li a {
            display: inline-block;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.85rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .navbar li a:hover {
            color: var(--text-main);
            background-color: rgba(255, 255, 255, 0.03);
        }

        .navbar li a.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background-color: rgba(18, 140, 126, 0.08);
        }

        .navbar li.logout-item {
            margin-left: auto;
        }

        .navbar li.logout-item a:hover {
            color: #ef4444;
            border-bottom-color: transparent;
        }

        /* --- Main Container --- */
        .main-container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 i {
            color: var(--primary);
        }

        /* --- Data Table --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        .custom-table th {
            background-color: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }

        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .custom-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .badge-id {
            background: rgba(18, 140, 126, 0.2);
            color: #4ade80;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .price-text {
            font-weight: 600;
            color: var(--accent);
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem;
            color: var(--text-muted);
        }

        /* --- Footer --- */
        .footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            background-color: var(--bg-dark);
            margin-top: auto;
        }

        .footer p {
            font-size: 0.8rem;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }
            .navbar {
                padding: 0 0.5rem;
            }
            .navbar li a {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
            .main-container {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="brand">
            <img src="../../assets/images/TREE.PNG" alt="PacificTree Logo" onerror="this.src='tree.PNG';">
            <h1 class="brand-title">PacificTree</h1>
        </div>
        <div class="user-info">
            <i class="fa fa-user-circle"></i>
            <span><?php echo htmlspecialchars($loggedin_session); ?></span>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul>
            <li><a href="../client/dashboard.php">Homepage</a></li>
            <li><a href="ProfileClient.php">Profile</a></li>
            <li><a class="active" href="PurchaseClient.php">Purchase</a></li>
            <li class="logout-item"><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <main class="main-container">
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-shopping-bag"></i> My Purchase History</h3>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Total Price (RM)</th>
                            <th>Date Sold</th>
                            <th>Block ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($conn) {
                                // Joined query to fetch sales and purchase/block details in one step
                                $sql = "SELECT s.SaleID, s.TotalPrice, s.DateSold, p.BlockID 
                                        FROM sale s 
                                        LEFT JOIN purchase p ON s.SaleID = p.SaleID 
                                        WHERE s.ClientID = " . intval($loggedin_id) . " 
                                        ORDER BY s.DateSold DESC";
                                
                                $result = mysqli_query($conn, $sql);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><span class="badge-id">#<?php echo htmlspecialchars($row['SaleID']); ?></span></td>
                                            <td class="price-text">RM <?php echo number_format($row['TotalPrice'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['DateSold']); ?></td>
                                            <td><?php echo !empty($row['BlockID']) ? htmlspecialchars($row['BlockID']) : 'N/A'; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="empty-state">No purchase records found.</td></tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="empty-state">Database connection unavailable.</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> PACIFICTREE. ALL RIGHTS RESERVED.</p>
    </footer>

</body>
</html>