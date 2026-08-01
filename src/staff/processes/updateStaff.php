<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php

    $updateID = isset($_GET['updateID']) ? intval($_GET['updateID']) : 0;
    global $conn;

    // Fetch existing staff details once to prepopulate input values cleanly
    $staffData = null;
    if ($updateID > 0) {
        $fetchSql = "SELECT * FROM staff WHERE UserID = $updateID";
        $fetchResult = mysqli_query($conn, $fetchSql);
        if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
            $staffData = mysqli_fetch_assoc($fetchResult);
        }
    }

    // Process Update Request
    if (isset($_POST['updateStaff'])) {   
        $employdate = mysqli_real_escape_string($conn, $_POST['employdate']);
        $salary     = mysqli_real_escape_string($conn, $_POST['salary']);   
        $IsManager  = mysqli_real_escape_string($conn, $_POST['IsManager']);
        
        $sql = "UPDATE staff SET EmployDate = '$employdate', Salary = '$salary', IsManager = '$IsManager' WHERE UserID = $updateID";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            header("Location: dashboard.php");
            exit();
        }
    }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Update Staff</title>

    <!-- FontAwesome & Google Font Inter -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">
    
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
                <!-- Breadcrumb -->
                <ul class="breadcrumb">
                    <li><a href="../dashboard.php">Users</a></li>
                    <li class="separator"><i class="fa fa-angle-right"></i></li>
                    <li class="active">Update Staff</li>
                </ul>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Staff Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- User ID Display -->
                            <div class="form-group">
                                <label>User ID</label>
                                <span class="form-control-static">#<?php echo htmlspecialchars($updateID); ?></span>
                            </div>

                            <!-- Employment Date -->
                            <div class="form-group">
                                <label for="employdate">Employ Date</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-calendar"></i>
                                    <input type="date" 
                                           class="form-control-input" 
                                           name="employdate" 
                                           id="employdate" 
                                           value="<?php echo isset($staffData['EmployDate']) ? htmlspecialchars($staffData['EmployDate']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Salary -->
                            <div class="form-group">
                                <label for="salary">Salary (RM)</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-usd"></i>
                                    <input type="number" 
                                           step="0.01" 
                                           class="form-control-input" 
                                           name="salary" 
                                           id="salary" 
                                           value="<?php echo isset($staffData['Salary']) ? htmlspecialchars($staffData['Salary']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Manager Status -->
                            <div class="form-group">
                                <label>Manager Status</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" 
                                               name="IsManager" 
                                               value="1" 
                                               <?php echo (isset($staffData['IsManager']) && $staffData['IsManager'] == 1) ? 'checked' : ''; ?> 
                                               required> Yes
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" 
                                               name="IsManager" 
                                               value="0" 
                                               <?php echo (isset($staffData['IsManager']) && $staffData['IsManager'] == 0) ? 'checked' : ''; ?> 
                                               required> No
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="submit" name="updateStaff" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <a href="../dashboard.php" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../../includes/footer.php'); ?>
        </main>
    </div>
</body>
</html>