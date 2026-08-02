<?php 
include(__DIR__ . '../../../config/dbConnect.php');

// Pre-fill blockID if passed via URL parameters
$prefilledBlockID = isset($_GET['blockID']) ? htmlspecialchars($_GET['blockID']) : '';
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Assign Block</title>

    <!-- FontAwesome & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <h1 class="topbar-title">Client & Sales Commercial Operations</h1>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <nav class="breadcrumb">
                    <a href="../../views/staff/sales.php">Clients & Sales</a>
                    <span class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i></span>
                    <span class="active">Assign Block</span>
                </nav>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-user-plus icon-green"></i> Assign Block to Client</h2>
                    </div>
                    <div class="card-body">
                        <form action="../../controllers/staff/inventoryController.php" method="POST">
                            <div class="form-group">
                                <label for="clientID">Client ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control-input" name="clientID" id="clientID" placeholder="Enter Client ID" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="blockID">Block ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-tree"></i>
                                    <input type="text" class="form-control-input" name="blockID" id="blockID" value="<?php echo $prefilledBlockID; ?>" placeholder="Enter Block ID" required>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="assignBlock" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Assign Block
                                </button>
                                <a href="../../views/staff/sales.php" class="btn btn-secondary">
                                    <i class="fa-solid fa-xmark"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>