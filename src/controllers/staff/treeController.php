<?php

include(__DIR__ . '../../../config/dbConnect.php');

global $conn;

// Determine action based on GET or POST parameter
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // 1. ADD NEW TREE
    // ==========================================
    case 'addTree':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTree'])) {
            $speciesname = trim($_POST['speciesname']);
            $latitude    = trim($_POST['latitude']);
            $longitude   = trim($_POST['longitude']);
            $blockID     = trim($_POST['blockID']);

            // Fetch the next TreeID
            $nextID = 1;
            $idSql = "SELECT MAX(TreeID) AS max_id FROM tree";
            $idResult = mysqli_query($conn, $idSql);
            if ($idResult && $row = mysqli_fetch_assoc($idResult)) {
                if ($row['max_id'] !== null) {
                    $nextID = (int)$row['max_id'] + 1;
                }
            }

            // Insert new tree
            $stmt = mysqli_prepare($conn, "INSERT INTO tree (TreeID, SpeciesName, Lattitude, Longitude, BlockID) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "issss", $nextID, $speciesname, $latitude, $longitude, $blockID);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: viewTree.php?msg=added");
                    exit();
                } else {
                    $_SESSION['error'] = "Execution failed: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['error'] = "Statement preparation failed: " . mysqli_error($conn);
            }

            header("Location: addTree.php");
            exit();
        }
        break;

    // ==========================================
    // 2. UPDATE TREE STATUS
    // ==========================================
    case 'updateTree':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTree'])) {
            $treeID      = $_GET['updateID'] ?? $_POST['treeID'] ?? null;
            $staffID     = trim($_POST['staffID']);
            $treeheight  = trim($_POST['treeheight']);
            $treediameter = trim($_POST['treediameter']);
            $treestatus  = trim($_POST['treestatus']);
            $treeimage   = trim($_POST['treeimage']);
            $updatedate  = trim($_POST['updatedate']);

            if (!$treeID) {
                header("Location: viewTree.php");
                exit();
            }

            // Fetch the next UpdateID
            $nextUpdateID = 1;
            $idSql = "SELECT MAX(UpdateID) AS max_id FROM treeupdate";
            $idResult = mysqli_query($conn, $idSql);
            if ($idResult && $row = mysqli_fetch_assoc($idResult)) {
                if ($row['max_id'] !== null) {
                    $nextUpdateID = (int)$row['max_id'] + 1;
                }
            }

            // Insert tree update status log
            $stmt = mysqli_prepare($conn, "INSERT INTO treeupdate (UpdateID, TreeID, StaffID, TreeHeight, TreeDiameter, TreeStatus, TreeImage, UpdateDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiisdsss", $nextUpdateID, $treeID, $staffID, $treeheight, $treediameter, $treestatus, $treeimage, $updatedate);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: viewTree.php?msg=updated");
                    exit();
                } else {
                    $_SESSION['error'] = "Execution failed: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['error'] = "Statement preparation failed: " . mysqli_error($conn);
            }

            header("Location: updateTree.php?updateID=" . urlencode($treeID));
            exit();
        }
        break;

    // ==========================================
    // 3. DELETE TREE RECORD
    // ==========================================
    case 'deleteTree':
        $deleteID = $_GET['deleteID'] ?? null;
        if ($deleteID) {
            $stmt = mysqli_prepare($conn, "DELETE FROM tree WHERE TreeID = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $deleteID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        header("Location: viewTree.php?msg=deleted");
        exit();

    // ==========================================
    // 4. DELETE TREE STATUS UPDATE
    // ==========================================
    case 'deleteTreeUpdate':
        $deleteID = $_GET['deleteID'] ?? null;
        if ($deleteID) {
            $stmt = mysqli_prepare($conn, "DELETE FROM treeupdate WHERE UpdateID = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $deleteID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        header("Location: viewTree.php?msg=update_deleted");
        exit();

    // ==========================================
    // DEFAULT REDIRECT
    // ==========================================
    default:
        header("Location: viewTree.php");
        exit();
}