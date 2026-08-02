<?php 
include(__DIR__ . '/../../config/dbConnect.php');

$error = '';
$treeID = $_GET['updateID'] ?? null;

// Redirect if no Tree ID is provided
if (!$treeID) {
    header("Location: ../views/staff/inventory.php");
    exit();
}

// -------------------------------------------------------------
// FETCH DATA: Get Tree details & Latest Tree Update Record
// -------------------------------------------------------------
$treeData = null;
$latestUpdate = null;

// 1. Fetch base tree details (including timber_grade)
$fetchTreeSql = "SELECT t.TreeID, t.SpeciesName, t.BlockID, t.timber_grade 
                FROM tree t 
                WHERE t.TreeID = ?";
$stmtFetchTree = mysqli_prepare($conn, $fetchTreeSql);
if ($stmtFetchTree) {
    mysqli_stmt_bind_param($stmtFetchTree, "i", $treeID);
    mysqli_stmt_execute($stmtFetchTree);
    $res = mysqli_stmt_get_result($stmtFetchTree);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $treeData = $row;
    }
    mysqli_stmt_close($stmtFetchTree);
}

// Redirect if tree does not exist
if (!$treeData) {
    header("Location: ../views/staff/inventory.php");
    exit();
}

// 2. Fetch the latest inspection log using StaffID
$fetchLogSql = "SELECT StaffID, TreeHeight, TreeDiameter, TreeStatus, TreeImage, UpdateDate 
                FROM treeupdate 
                WHERE TreeID = ? 
                ORDER BY UpdateDate DESC, UpdateID DESC 
                LIMIT 1";

$stmtFetchLog = mysqli_prepare($conn, $fetchLogSql);
if ($stmtFetchLog) {
    mysqli_stmt_bind_param($stmtFetchLog, "i", $treeID);
    mysqli_stmt_execute($stmtFetchLog);
    $logRes = mysqli_stmt_get_result($stmtFetchLog);
    if ($logRes && $logRow = mysqli_fetch_assoc($logRes)) {
        $latestUpdate = $logRow;
    }
    mysqli_stmt_close($stmtFetchLog);
}

// -------------------------------------------------------------
// PRE-FILL FORM VARIABLES
// -------------------------------------------------------------

// From 'tree' table
$timber_grade = $treeData['timber_grade'] ?? '';

// From 'treeupdate' table (fallback to empty defaults if no previous log exists)
$staffID      = $latestUpdate['StaffID'] ?? '';
$treeheight   = $latestUpdate['TreeHeight'] ?? '';
$treediameter = $latestUpdate['TreeDiameter'] ?? '';
$treestatus   = $latestUpdate['TreeStatus'] ?? '1';
$treeimage    = $latestUpdate['TreeImage'] ?? '';

// Format date strictly as 'Y-m-d' for the <input type="date"> element
if (!empty($latestUpdate['UpdateDate'])) {
    $updatedate = date('Y-m-d', strtotime($latestUpdate['UpdateDate']));
} else {
    $updatedate = date('Y-m-d');
}

// Fetch staff list for dropdown
$staffList = [];
$staffQuery = "SELECT UserID FROM staff ORDER BY UserID ASC";
$staffResult = mysqli_query($conn, $staffQuery);
if ($staffResult) {
    while ($row = mysqli_fetch_assoc($staffResult)) {
        $staffList[] = $row;
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Update Tree Status</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        
        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Content Wrapper -->
        <main class="main-content">
            
            <header class="topbar">
                <h1 class="topbar-title">Tree Block Inventory & Spatial Mapping</h1>
            </header>

            <div class="content-body">
                
                <!-- Breadcrumbs -->
                <nav>
                    <ul class="breadcrumb">
                        <li><a href="../../views/staff/inventory.php">Inventory</a></li>
                        <li class="separator"><i class="fa-solid fa-angle-right"></i></li>
                        <li class="active">Update Tree #<?php echo htmlspecialchars($treeID); ?> (<?php echo htmlspecialchars($treeData['SpeciesName']); ?>)</li>
                    </ul>
                </nav>

                <!-- Form Card Component -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-pen-to-square"></i> Record Tree Update Details — Tree #<?php echo htmlspecialchars($treeID); ?>
                        </h2>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="../../controllers/staff/inventoryController.php?action=updateTree">
                            <!-- Hidden Tree ID Input -->
                            <input type="hidden" name="treeID" value="<?php echo htmlspecialchars($treeID); ?>">

                            <!-- Staff ID / User ID Select Dropdown -->
                            <div class="form-group">
                                <label for="staffID">Staff Member</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-id-badge"></i>
                                    <select class="form-control-input" name="staffID" id="staffID" required>
                                        <option value="" disabled <?php echo empty($staffID) ? 'selected' : ''; ?>>Select Staff ID</option>
                                        <?php foreach ($staffList as $staff): ?>
                                            <option value="<?php echo htmlspecialchars($staff['UserID']); ?>" 
                                                <?php echo ($staffID == $staff['UserID']) ? 'selected' : ''; ?>>
                                                Staff ID #<?php echo htmlspecialchars($staff['UserID']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Timber Grade Dropdown -->
                            <div class="form-group">
                                <label for="timber_grade">Timber Grade</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-award"></i>
                                    <select class="form-control-input" name="timber_grade" id="timber_grade" required>
                                        <option value="" disabled <?php echo empty($timber_grade) ? 'selected' : ''; ?>>Select Grade</option>
                                        <option value="A" <?php echo ($timber_grade == 'A') ? 'selected' : ''; ?>>Grade A (Premium)</option>
                                        <option value="B" <?php echo ($timber_grade == 'B') ? 'selected' : ''; ?>>Grade B (Standard)</option>
                                        <option value="C" <?php echo ($timber_grade == 'C') ? 'selected' : ''; ?>>Grade C (Utility)</option>
                                        <option value="D" <?php echo ($timber_grade == 'D') ? 'selected' : ''; ?>>Grade D (Low)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tree Height -->
                            <div class="form-group">
                                <label for="treeheight">Tree Height (m)</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-ruler-vertical"></i>
                                    <input type="number" step="0.01" class="form-control-input" name="treeheight" id="treeheight" placeholder="e.g. 12.5" value="<?php echo htmlspecialchars($treeheight); ?>" required>
                                </div>
                            </div>

                            <!-- Tree Diameter -->
                            <div class="form-group">
                                <label for="treediameter">Tree Diameter (cm)</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-ruler-horizontal"></i>
                                    <input type="number" step="0.01" class="form-control-input" name="treediameter" id="treediameter" placeholder="e.g. 45.0" value="<?php echo htmlspecialchars($treediameter); ?>" required>
                                </div>
                            </div>

                            <!-- Tree Status Select Dropdown (1 = Healthy, 0 = Requires Attention) -->
                            <div class="form-group">
                                <label for="treestatus">Tree Status</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                    <select class="form-control-input" name="treestatus" id="treestatus" required>
                                        <option value="1" <?php echo ($treestatus == '1' || $treestatus === 1) ? 'selected' : ''; ?>>1 - Healthy</option>
                                        <option value="0" <?php echo ($treestatus == '0' || $treestatus === 0) ? 'selected' : ''; ?>>0 - Requires Attention</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image URL / Path -->
                            <div class="form-group">
                                <label for="treeimage">Image URL / File Path</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-image"></i>
                                    <input type="text" class="form-control-input" name="treeimage" id="treeimage" placeholder="path/to/image.jpg" value="<?php echo htmlspecialchars($treeimage); ?>" required>
                                </div>
                            </div>

                            <!-- Update Date -->
                            <div class="form-group">
                                <label for="updatedate">Update Date</label>
                                <div class="input-wrapper">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <input type="date" class="form-control-input" name="updatedate" id="updatedate" value="<?php echo htmlspecialchars($updatedate); ?>" required>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="submit" name="updateTree" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Record
                                </button>
                                <a href="../../views/staff/inventory.php" class="btn btn-outline-danger">Cancel</a>
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