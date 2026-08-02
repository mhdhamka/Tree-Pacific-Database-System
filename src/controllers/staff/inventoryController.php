<?php
session_start();
include(__DIR__ . '/../../config/dbConnect.php');

$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ============================================================================
// 1. TREE ACTIONS
// ============================================================================
// --- ADD TREE ---
if (isset($_POST['addTree']) || $action === 'addTree') {
    $speciesname  = trim($_POST['speciesname'] ?? '');
    $timber_grade = trim($_POST['timber_grade'] ?? $_POST['timberGrade'] ?? '');
    
    // Check all possible form field names for height and diameter
    $treeheight   = floatval($_POST['treeheight'] ?? $_POST['height'] ?? $_POST['tree_height'] ?? 0);
    $treediameter = floatval($_POST['treediameter'] ?? $_POST['diameter'] ?? $_POST['tree_diameter'] ?? 0);
    
    $latitude     = trim($_POST['latitude'] ?? '');
    $longitude    = trim($_POST['longitude'] ?? '');
    $blockID      = intval($_POST['blockID'] ?? 0);

    if (empty($speciesname) || empty($timber_grade) || $treeheight <= 0 || $treediameter <= 0 || empty($latitude) || empty($longitude) || $blockID <= 0) {
        $error = "Please fill in all required fields accurately. Height and diameter must be greater than 0.";
    } else {
        mysqli_begin_transaction($conn);

        try {
            // 1. Get next TreeID
            $nextTreeID = 1;
            $idSql = "SELECT MAX(TreeID) AS max_id FROM tree";
            $idResult = mysqli_query($conn, $idSql);
            if ($idResult && $row = mysqli_fetch_assoc($idResult)) {
                if ($row['max_id'] !== null) {
                    $nextTreeID = (int)$row['max_id'] + 1;
                }
            }

            // 2. Insert into `tree` table
            $stmtTree = mysqli_prepare($conn, "INSERT INTO tree (TreeID, SpeciesName, timber_grade, Lattitude, Longitude, BlockID) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmtTree) {
                throw new Exception("Tree query prep failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmtTree, "issssi", $nextTreeID, $speciesname, $timber_grade, $latitude, $longitude, $blockID);
            if (!mysqli_stmt_execute($stmtTree)) {
                throw new Exception("Tree insert failed: " . mysqli_stmt_error($stmtTree));
            }
            mysqli_stmt_close($stmtTree);

            // 3. Get next UpdateID
            $nextUpdateID = 1;
            $uSql = "SELECT MAX(UpdateID) AS max_id FROM treeupdate";
            $uResult = mysqli_query($conn, $uSql);
            if ($uResult && $uRow = mysqli_fetch_assoc($uResult)) {
                if ($uRow['max_id'] !== null) {
                    $nextUpdateID = (int)$uRow['max_id'] + 1;
                }
            }

            // 4. Insert initial height & diameter log into `treeupdate`
            $defaultStaffID = 1; // Change to $_SESSION['UserID'] if using session
            $defaultStatus  = 1; // 1 = Healthy
            $defaultImage   = 'assets/images/default-tree.jpg';
            $currentDate    = date('Y-m-d H:i:s');

            $stmtLog = mysqli_prepare($conn, "INSERT INTO treeupdate (UpdateID, TreeID, StaffID, TreeHeight, TreeDiameter, TreeStatus, TreeImage, UpdateDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtLog) {
                throw new Exception("Log query prep failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmtLog, "iiiddiss", $nextUpdateID, $nextTreeID, $defaultStaffID, $treeheight, $treediameter, $defaultStatus, $defaultImage, $currentDate);
            if (!mysqli_stmt_execute($stmtLog)) {
                throw new Exception("Log insert failed: " . mysqli_stmt_error($stmtLog));
            }
            mysqli_stmt_close($stmtLog);

            mysqli_commit($conn);

            header("Location: ../../views/staff/inventory.php");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error saving tree: " . $e->getMessage();
            exit();
        }
    }
}


// --- UPDATE TREE LOG / INSPECTION ---
if (isset($_POST['updateTree']) || $action === 'updateTree' || $action === 'update') {
    $treeID       = isset($_GET['updateID']) ? intval($_GET['updateID']) : intval($_POST['treeID'] ?? 0);
    $staffID      = intval($_POST['staffID'] ?? 0);
    $timber_grade = trim($_POST['timber_grade'] ?? '');
    $treeheight   = floatval($_POST['treeheight'] ?? 0.0);
    $treediameter = floatval($_POST['treediameter'] ?? 0.0);
    $treestatus   = intval($_POST['treestatus'] ?? 1); // 1 = Healthy, 0 = Requires Attention
    $treeimage    = trim($_POST['treeimage'] ?? '');
    $updatedate   = !empty($_POST['updatedate']) ? trim($_POST['updatedate']) : date('Y-m-d H:i:s');

    if ($treeID <= 0 || $staffID <= 0 || empty($timber_grade)) {
        $error = "Please complete all mandatory fields correctly.";
    } else {
        // Begin transaction
        mysqli_begin_transaction($conn);

        try {
            // 1. Update timber_grade in the `tree` table
            $stmtTree = mysqli_prepare($conn, "UPDATE tree SET timber_grade = ? WHERE TreeID = ?");
            if (!$stmtTree) {
                throw new Exception("Tree update statement preparation failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmtTree, "si", $timber_grade, $treeID);
            if (!mysqli_stmt_execute($stmtTree)) {
                throw new Exception("Failed to update tree timber grade: " . mysqli_stmt_error($stmtTree));
            }
            mysqli_stmt_close($stmtTree);

            // 2. Generate the next UpdateID
            $nextUpdateID = 1;
            $idSql = "SELECT MAX(UpdateID) AS max_id FROM treeupdate";
            $idResult = mysqli_query($conn, $idSql);
            if ($idResult && $row = mysqli_fetch_assoc($idResult)) {
                if ($row['max_id'] !== null) {
                    $nextUpdateID = (int)$row['max_id'] + 1;
                }
            }

            // 3. Insert new log record into `treeupdate` table
            $stmtLog = mysqli_prepare($conn, "INSERT INTO treeupdate (UpdateID, TreeID, StaffID, TreeHeight, TreeDiameter, TreeStatus, TreeImage, UpdateDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtLog) {
                throw new Exception("Log statement preparation failed: " . mysqli_error($conn));
            }

            // Bind Types: UpdateID(i), TreeID(i), StaffID(i), Height(d), Diameter(d), Status(i), Image(s), Date(s)
            mysqli_stmt_bind_param($stmtLog, "iiiddiss", $nextUpdateID, $treeID, $staffID, $treeheight, $treediameter, $treestatus, $treeimage, $updatedate);
            if (!mysqli_stmt_execute($stmtLog)) {
                throw new Exception("Failed to record inspection log: " . mysqli_stmt_error($stmtLog));
            }
            mysqli_stmt_close($stmtLog);

            // Commit transaction
            mysqli_commit($conn);

            header("Location: ../../views/staff/inventory.php");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

// --- DELETE TREE ---
if ($action === 'deleteTree' || $action === 'delete' || isset($_GET['deleteTreeID'])) {
    $deleteID = isset($_GET['deleteID']) ? intval($_GET['deleteID']) : intval($_GET['deleteTreeID'] ?? 0);

    if ($deleteID > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM tree WHERE TreeID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $deleteID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: ../../views/staff/inventory.php");
    exit();
}

// --- DELETE TREE LOG / INSPECTION ---
if ($action === 'deleteTreeUpdate') {
    $deleteID = intval($_GET['deleteID'] ?? 0);
    
    if ($deleteID > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM treeupdate WHERE UpdateID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $deleteID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    header("Location: ../../views/staff/inventory.php");
    exit();
}


// ============================================================================
// 2. BLOCK ACTIONS
// ============================================================================

// --- ADD BLOCK ---
if (isset($_POST['addBlock']) || $action === 'addBlock' || $action === 'add') {
    $baseprice = trim($_POST['baseprice'] ?? '');
    $orchardID = isset($_POST['orchardID']) ? intval($_POST['orchardID']) : 0;

    if (!empty($baseprice) && $orchardID > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO block (BasePrice, OrchardID) VALUES (?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "di", $baseprice, $orchardID);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: ../../views/staff/inventory.php");
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $error = "Please fill in all required fields and select a valid Orchard.";
    }
}

// --- UPDATE BLOCK ---
if (isset($_POST['updateBlock']) || $action === 'updateBlock' || $action === 'update') {
    $updateID  = isset($_GET['updateID']) ? intval($_GET['updateID']) : intval($_POST['blockID'] ?? 0);
    $baseprice = trim($_POST['baseprice'] ?? '');
    $orchardID = isset($_POST['orchardID']) ? intval($_POST['orchardID']) : 0;

    if ($updateID <= 0) {
        header("Location: ../../views/staff/inventory.php");
        exit();
    }

    if (!empty($baseprice) && $orchardID > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE block SET BasePrice = ?, OrchardID = ? WHERE BlockID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "dii", $baseprice, $orchardID, $updateID);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: ../../views/staff/inventory.php");
                exit();
            } else {
                $error = "Update Failed: " . mysqli_error($conn);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $error = "All fields are required and a valid Orchard must be selected.";
    }
}


// --- DELETE BLOCK ---
if ($action === 'deleteBlock') {
    $deleteID = intval($_GET['deleteID'] ?? $_GET['deleteBlockID'] ?? 0);

    if ($deleteID > 0 && isset($conn)) {
        $stmt = mysqli_prepare($conn, "DELETE FROM block WHERE BlockID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $deleteID);
            
            // OPTIONAL: Catch foreign key errors (e.g., trees existing inside this block)
            if (!mysqli_stmt_execute($stmt)) {
                // If it fails due to Foreign Key Constraint, you can debug here
                // echo mysqli_stmt_error($stmt); die();
            }
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: ../../views/staff/inventory.php");
    exit();
}


// ============================================================================
// 3. HELPER DATA FETCHING (For views utilizing this controller)
// ============================================================================

// Fetch Active User Name safely
$username = $_SESSION['username'] ?? "Staff User";
$userQuery = mysqli_query($conn, "SELECT Username FROM user WHERE logStatus = 1 AND UserType = 'S' LIMIT 1");
if ($userQuery && mysqli_num_rows($userQuery) > 0) {
    $userData = mysqli_fetch_assoc($userQuery);
    $username = $userData['Username'];
}
?>