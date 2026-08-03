<?php

include(__DIR__ . '/../../config/dbConnect.php');

global $conn;

// Determine action based on request parameters
$action = isset($_GET['action']) ? $_GET['action'] : '';

// ---------------------------------------------------------------------
// 1. DELETE COMPANY
// ---------------------------------------------------------------------
if ($action === 'delete' && isset($_GET['deleteID'])) {
    $deleteID = intval($_GET['deleteID']);

    $stmt = mysqli_prepare($conn, "DELETE FROM company WHERE CompanyID = ?");
    mysqli_stmt_bind_param($stmt, "i", $deleteID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../../views/staff/sales.php?status=deleted");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($conn));
    }
}

// ---------------------------------------------------------------------
// 2. ADD COMPANY
// ---------------------------------------------------------------------
if (isset($_POST['addCompany'])) {
    $companyname = trim($_POST['companyname']);
    $companyorchard = trim($_POST['companyorchard']);

    $stmt = mysqli_prepare($conn, "INSERT INTO company (CompanyName, OrchardID) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $companyname, $companyorchard);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../../views/staff/sales.php?status=added");
        exit();
    } else {
        die("Error adding record: " . mysqli_error($conn));
    }
}

// ---------------------------------------------------------------------
// 3. UPDATE COMPANY
// ---------------------------------------------------------------------
if (isset($_POST['updateCompany']) && isset($_GET['updateID'])) {
    $updateID = intval($_GET['updateID']);
    $companyname = trim($_POST['companyname']);
    $orchardID = trim($_POST['orchardID']);

    $stmt = mysqli_prepare($conn, "UPDATE company SET CompanyName = ?, OrchardID = ? WHERE CompanyID = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $companyname, $orchardID, $updateID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../../views/staff/sales.php?status=updated");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
}

// ---------------------------------------------------------------------
// 4. ASSIGN BLOCK TO CLIENT
// ---------------------------------------------------------------------
if (isset($_POST['assignBlock'])) {
    $clientID = trim($_POST['clientID']);
    $blockID = trim($_POST['blockID']);

    // Fetch BasePrice for the block
    $stmt = mysqli_prepare($conn, "SELECT BasePrice FROM block WHERE BlockID = ?");
    mysqli_stmt_bind_param($stmt, "s", $blockID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $price = $row['BasePrice'];
        mysqli_stmt_close($stmt);

        // Insert into sale table
        $stmtSale = mysqli_prepare($conn, "INSERT INTO sale (ClientID, TotalPrice, DateSold) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($stmtSale, "sd", $clientID, $price);

        if (mysqli_stmt_execute($stmtSale)) {
            $saleID = mysqli_insert_id($conn);
            mysqli_stmt_close($stmtSale);

            // Insert into purchase table
            $stmtPurchase = mysqli_prepare($conn, "INSERT INTO purchase (SaleID, BlockID, SellingPrice) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmtPurchase, "isd", $saleID, $blockID, $price);

            if (mysqli_stmt_execute($stmtPurchase)) {
                mysqli_stmt_close($stmtPurchase);
                header("Location: ../../views/staff/sales.php?status=assigned");
                exit();
            } else {
                die("Error recording purchase: " . mysqli_error($conn));
            }
        } else {
            die("Error recording sale: " . mysqli_error($conn));
        }
    } else {
        die("Invalid Block ID specified.");
    }
}

// ---------------------------------------------------------------------
// 5. REASSIGN BLOCK
// ---------------------------------------------------------------------
if (isset($_POST['reassignBlock'])) {
    $newUserID = trim($_POST['updateID']);
    $blockID = isset($_GET['blockID']) ? trim($_GET['blockID']) : '';

    if (empty($blockID) || empty($newUserID)) {
        die("Missing Block ID or New User ID.");
    }

    // Fetch BasePrice for the block
    $stmt = mysqli_prepare($conn, "SELECT BasePrice FROM block WHERE BlockID = ?");
    mysqli_stmt_bind_param($stmt, "s", $blockID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $price = $row['BasePrice'];
        mysqli_stmt_close($stmt);

        // Create new Sale record for reassignment
        $stmtSale = mysqli_prepare($conn, "INSERT INTO sale (ClientID, TotalPrice, DateSold) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($stmtSale, "sd", $newUserID, $price);

        if (mysqli_stmt_execute($stmtSale)) {
            $newSaleID = mysqli_insert_id($conn);
            mysqli_stmt_close($stmtSale);

            // Create new Purchase record linking the reassigned block
            $stmtPurchase = mysqli_prepare($conn, "INSERT INTO purchase (SaleID, BlockID, SellingPrice) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmtPurchase, "isd", $newSaleID, $blockID, $price);

            if (mysqli_stmt_execute($stmtPurchase)) {
                mysqli_stmt_close($stmtPurchase);
                header("Location: ../../views/staff/sales.php?status=reassigned");
                exit();
            } else {
                die("Error updating purchase: " . mysqli_error($conn));
            }
        } else {
            die("Error processing sale: " . mysqli_error($conn));
        }
    } else {
        die("Block record not found.");
    }
}

// Helper function to fetch single company details for Update forms
function getCompanyById($conn, $companyID) {
    $stmt = mysqli_prepare($conn, "SELECT CompanyID, CompanyName, OrchardID FROM company WHERE CompanyID = ?");
    mysqli_stmt_bind_param($stmt, "i", $companyID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>