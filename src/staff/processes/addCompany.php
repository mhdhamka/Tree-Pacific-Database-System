<?php 
include(__DIR__ . '../../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Add Company</title>
    
    <!-- Fonts & Icons -->
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
        <?php include(__DIR__ . '../../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/sales.php">Clients & Sales</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Add Company</li>
                </ul>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Add New Company</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../../controllers/staff/companyController.php?action=add">
                            <div class="form-group">
                                <label for="companyname">Company Name</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-building"></i>
                                    <input type="text" name="companyname" id="companyname" class="form-control-input" placeholder="Enter company name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="companyorchard">Orchard ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-map"></i>
                                    <input type="text" name="companyorchard" id="companyorchard" class="form-control-input" placeholder="Enter orchard ID" required>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="addCompany" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Company
                                </button>
                                <a href="../../views/staff/sales.php" class="btn btn-outline-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../../../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>