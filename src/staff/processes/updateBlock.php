<?php 
include(__DIR__ . '/../../config/dbConnect.php');

$error = '';
$updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;

if ($updateID <= 0) {
    header("Location: viewBlock.php");
    exit();
}

// Handle Form Submission
if (isset($_POST['updateBlock'])) {   
    $baseprice = trim($_POST['baseprice']);
    $orchardID = trim($_POST['orchardID']);

    if (!empty($baseprice) && !empty($orchardID)) {
        // Prepared Statement for Update
        $stmt = mysqli_prepare($conn, "UPDATE block SET BasePrice = ?, OrchardID = ? WHERE BlockID = ?");
        mysqli_stmt_bind_param($stmt, "dii", $baseprice, $orchardID, $updateID);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: viewBlock.php");
            exit();
        } else {
            $error = "Update Failed: " . mysqli_error($conn);
        }
    } else {
        $error = "All fields are required.";
    }
}

// Fetch Existing Record Data safely
$stmt = mysqli_prepare($conn, "SELECT BlockID, BasePrice, OrchardID FROM block WHERE BlockID = ?");
mysqli_stmt_bind_param($stmt, "i", $updateID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: viewBlock.php");
    exit();
}

$block = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Fetch Active User Name safely
$username = "Staff User";
$userQuery = mysqli_query($conn, "SELECT Username FROM user WHERE logStatus = 1 AND UserType = 'S' LIMIT 1");
if ($userQuery && mysqli_num_rows($userQuery) > 0) {
    $userData = mysqli_fetch_assoc($userQuery);
    $username = $userData['Username'];
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update Block</title>

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
            <header class="topbar">
                <h1 class="topbar-title">Tree Block Inventory & Spatial Mapping</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb -->
                <ul class="breadcrumb">
                    <li><a href="../../views/staff/inventory.php">Inventory</a></li>
                    <li class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></li>
                    <li class="active">Update Block</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Block Details</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="../../controllers/staff/inventoryController.php?action=updateBlock">
                            <!-- Hidden blockID input so POST retains the target ID -->
                            <input type="hidden" name="blockID" value="<?php echo htmlspecialchars($block['BlockID']); ?>">

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Block ID</label>
                                    <div class="read-only-box">
                                        #<?php echo htmlspecialchars($block['BlockID']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="baseprice">Base Price (RM)</label>
                                    <div class="input-wrapper">
                                        <i class="fa-solid fa-dollar-sign"></i>
                                        <input type="number" step="0.01" name="baseprice" id="baseprice" class="form-control-input" value="<?php echo htmlspecialchars($block['BasePrice']); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="orchardID">Orchard ID</label>
                                    <div class="input-wrapper">
                                        <i class="fa-solid fa-map-pin"></i>
                                        <select name="orchardID" id="orchardID" class="form-control-input" required>
                                            <option value="" disabled>Select an Orchard</option>
                                            <?php
                                            $orchardSql = "SELECT OrchardID FROM orchard ORDER BY OrchardID ASC";
                                            $orchardRes = mysqli_query($conn, $orchardSql);
                                            if ($orchardRes && mysqli_num_rows($orchardRes) > 0) {
                                                while ($oRow = mysqli_fetch_assoc($orchardRes)) {
                                                    $oID = htmlspecialchars($oRow['OrchardID']);
                                                    $isSelected = ($oID == $block['OrchardID']) ? 'selected' : '';
                                                    echo "<option value='{$oID}' {$isSelected}>Orchard {$oID}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" name="updateBlock" class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk"></i> Update Block
                                    </button>
                                    <a href="../../views/staff/inventory.php" class="btn btn-secondary">Cancel</a>
                                </div>
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