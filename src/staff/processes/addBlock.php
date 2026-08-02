<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Add Block</title>

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
        <?php include(__DIR__ . '../../../includes/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Block Inventory & Spatial Mapping</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/inventory.php">Inventory</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Add Block</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Add New Block Record</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="../../controllers/staff/inventoryController.php?action=addBlock">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="baseprice">Base Price (RM)</label>
                                    <div class="input-wrapper">
                                        <i class="fa-solid fa-dollar-sign"></i>
                                        <input type="number" step="0.01" name="baseprice" id="baseprice" class="form-control-input" placeholder="e.g. 1500.00" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="orchardID">Orchard ID</label>
                                    <div class="input-wrapper">
                                        <i class="fa-solid fa-map-pin"></i>
                                        <select name="orchardID" id="orchardID" class="form-control-input" required>
                                            <option value="" disabled selected>Select an Orchard</option>
                                            <?php
                                            $orchardSql = "SELECT OrchardID FROM orchard ORDER BY OrchardID ASC";
                                            $orchardRes = mysqli_query($conn, $orchardSql);
                                            if ($orchardRes && mysqli_num_rows($orchardRes) > 0) {
                                                while ($oRow = mysqli_fetch_assoc($orchardRes)) {
                                                    $oID = htmlspecialchars($oRow['OrchardID']);
                                                    echo "<option value='{$oID}'>Orchard {$oID}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" name="addBlock" class="btn btn-primary">
                                        <i class="fa-solid fa-plus"></i> Submit Block
                                    </button>
                                    <a href="../../views/staff/inventory.php" class="btn btn-outline-danger">Cancel</a>
                                </div>
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