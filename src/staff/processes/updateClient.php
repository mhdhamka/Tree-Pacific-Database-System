<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php

    $updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;
    global $conn;

    // Fetch existing client details once to pre-fill input values safely
    $clientData = null;
    if ($updateID > 0) {
        $fetchSql = "SELECT * FROM client WHERE UserID = $updateID";
        $fetchResult = mysqli_query($conn, $fetchSql);
        if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
            $clientData = mysqli_fetch_assoc($fetchResult);
        }
    }

    // Process Update Request
    if (isset($_POST['updateClient'])) {   
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $country = mysqli_real_escape_string($conn, $_POST['country']); 
        $photo   = mysqli_real_escape_string($conn, $_POST['photo']);
        
        $sql = "UPDATE client SET Address = '$address', Country = '$country', Photo = '$photo' WHERE UserID = $updateID";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            header("Location: ../dashboard.php");
            exit();
        }
    }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update Client</title>

    <!-- FontAwesome & Google Font Inter -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '../../includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <ul class="breadcrumb">
                    <li><a href="../dashboard.php">Users</a></li>
                    <li class="separator"><i class="fa fa-angle-right"></i></li>
                    <li class="active">Update Client</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Client Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- User ID Display -->
                            <div class="form-group">
                                <label>User ID</label>
                                <span class="form-control-static">#<?php echo htmlspecialchars($updateID); ?></span>
                            </div>

                            <!-- Address Input -->
                            <div class="form-group">
                                <label for="address">Address</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-address-book"></i>
                                    <input type="text" 
                                           class="form-control-input" 
                                           name="address" 
                                           id="address" 
                                           value="<?php echo isset($clientData['Address']) ? htmlspecialchars($clientData['Address']) : ''; ?>" 
                                           placeholder="Enter full address" 
                                           required>
                                </div>
                            </div>

                            <!-- Country Input -->
                            <div class="form-group">
                                <label for="country">Country</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" 
                                           class="form-control-input" 
                                           name="country" 
                                           id="country" 
                                           value="<?php echo isset($clientData['Country']) ? htmlspecialchars($clientData['Country']) : ''; ?>" 
                                           placeholder="Enter country" 
                                           required>
                                </div>
                            </div>

                            <!-- Photo Path Input -->
                            <div class="form-group">
                                <label for="photo">Photo (Filename / URL)</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-file-image-o"></i>
                                    <input type="text" 
                                           class="form-control-input" 
                                           name="photo" 
                                           id="photo" 
                                           value="<?php echo isset($clientData['Photo']) ? htmlspecialchars($clientData['Photo']) : ''; ?>" 
                                           placeholder="e.g. avatar.jpg">
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="form-actions">
                                <button type="submit" name="updateClient" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <a href="../dashboard.php" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="app-footer">
                <div>&copy; <?php echo date("Y"); ?> TreePacific Management System. All rights reserved.</div>
            </footer>
        </main>
    </div>
</body>
</html>