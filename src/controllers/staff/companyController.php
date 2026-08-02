<?php
// Include the database connection file
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
        header("Location: ../../views/staff/companies.php?status=deleted");
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

    // Prepared statement to prevent SQL injection and let DB handle Auto-Increment ID
    $stmt = mysqli_prepare($conn, "INSERT INTO company (CompanyName, OrchardID) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $companyname, $companyorchard);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../../views/staff/companies.php?status=added");
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
        header("Location: ../../views/staff/companies.php?status=updated");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($conn));
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