<?php 
include(__DIR__ . '/../../config/dbConnect.php');

// Safely retrieve the Company ID from GET parameters
$updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : (isset($_POST['companyID']) ? intval($_POST['companyID']) : 0);

// Fetch Existing Record Data
global $conn;
$companyData = null;

if ($updateID > 0) {
    $sqlFetch = "SELECT CompanyID, CompanyName, OrchardID FROM company WHERE CompanyID = ?";
    $stmt = mysqli_prepare($conn, $sqlFetch);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $updateID);
        mysqli_stmt_execute($stmt);
        $resultFetch = mysqli_stmt_get_result($stmt);
        if ($resultFetch && mysqli_num_rows($resultFetch) > 0) {
            $companyData = mysqli_fetch_assoc($resultFetch);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update Company</title>
    
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
        <?php include (__DIR__ . '../../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/companies.php">Companies</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Update Company</li>
                </ul>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($messageType ?? 'info'); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Company Record</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../../controllers/staff/companyController.php?action=update&updateID=<?php echo $updateID; ?>">
                            
                            <!-- Hidden input to pass CompanyID during form submit -->
                            <input type="hidden" name="companyID" value="<?php echo htmlspecialchars($companyData['CompanyID'] ?? $updateID); ?>">

                            <div class="form-group">
                                <label>Company ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-id-badge"></i>
                                    <input type="text" class="form-control-input" value="<?php echo htmlspecialchars($companyData['CompanyID'] ?? ''); ?>" disabled style="background-color: var(--bg-body); color: var(--secondary);">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="companyname">Company Name</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-building"></i>
                                    <input type="text" name="companyname" id="companyname" class="form-control-input" value="<?php echo htmlspecialchars($companyData['CompanyName'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="orchardID">Orchard ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-map"></i>
                                    <input type="text" name="orchardID" id="orchardID" class="form-control-input" value="<?php echo htmlspecialchars($companyData['OrchardID'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="updateCompany" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <a href="../../views/staff/companies.php" class="btn btn-outline-danger">Cancel</a>
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