<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php

$alertMessage = "";
$alertType = "";

if (isset($_POST['addStaff'])) {
    $userID = mysqli_real_escape_string($conn, $_POST['userID']);
    $employdate = mysqli_real_escape_string($conn, $_POST['employdate']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $IsManager = mysqli_real_escape_string($conn, $_POST['IsManager']);

    $sql = "INSERT INTO staff(UserID, EmployDate, Salary, IsManager)
            VALUES('$userID', '$employdate', '$salary', '$IsManager')";
            
    if (mysqli_query($conn, $sql)) { 
        $alertMessage = "Staff member added successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error adding staff: " . mysqli_error($conn);
        $alertType = "danger";
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Staff Management</title>
    
    <!-- Icons and Fonts -->
    <script src="https://use.fontawesome.com/59805f286a.js"></script>
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include(__DIR__ . '../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb -->
                <ul class="breadcrumb">
                    <li><a href="viewUser.php">Users</a></li>
                    <li class="separator"><i class="fa fa-angle-right"></i></li>
                    <li class="active">Add Staff</li>
                </ul>

                <?php if (!empty($alertMessage)): ?>
                    <div class="alert alert-<?php echo $alertType; ?>">
                        <?php echo $alertMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Section -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Register New Staff Details</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="userID">User ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-id-badge"></i>
                                    <input type="text" name="userID" id="userID" class="form-control-input" placeholder="Enter user ID" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="employdate">Employment Date</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-calendar"></i>
                                    <input type="date" name="employdate" id="employdate" class="form-control-input" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="salary">Salary ($)</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-dollar"></i>
                                    <input type="text" name="salary" id="salary" class="form-control-input" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Manager Status</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="IsManager" value="1" required>
                                        <span>Yes</span>
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="IsManager" value="0" required>
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>

                            <div style="margin-top: 28px;">
                                <button type="submit" name="addStaff" class="btn btn-primary">
                                    <i class="fa fa-check"></i> Submit Staff Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Staff Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">System Staff Accounts</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Password Hash</th>
                                    <th>Real Name</th>
                                    <th>User Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM user WHERE UserType = 'S'";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>".htmlspecialchars($row['UserID'])."</td>
                                                    <td><strong>".htmlspecialchars($row['Username'])."</strong></td>
                                                    <td>".htmlspecialchars($row['Email'])."</td>
                                                    <td><code>".htmlspecialchars($row['PasswordHash'])."</code></td>
                                                    <td>".htmlspecialchars($row['RealName'])."</td>
                                                    <td><span class='badge-usertype'>".htmlspecialchars($row['UserType'])."</span></td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' style='text-align:center;'>No staff user records found.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Staff Record Details Table -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Employment Records</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Employment Date</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM staff";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>".htmlspecialchars($row['UserID'])."</td>
                                                    <td>".htmlspecialchars($row['EmployDate'])."</td>
                                                    <td><span class='price-text'>$".htmlspecialchars($row['Salary'])."</span></td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' style='text-align:center;'>No staff records found.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '../../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>