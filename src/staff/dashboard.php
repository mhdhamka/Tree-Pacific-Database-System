<?php 

include (__DIR__ . '../../config/dbConnect.php');

?>

<?php
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'] ?? 0;
$totalClients = $conn->query("SELECT COUNT(*) AS total FROM client")->fetch_assoc()['total'] ?? 0;
$totalStaff = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Users</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include __DIR__ . '../includes/sidebar.php'; ?>

        <!-- Main Dashboard View -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">

                <!-- Stats Overview Row -->
                <div class="stats-grid">
                    <div class="card stats-card">
                        <i class="fa-solid fa-users fa-2x icon-blue"></i>
                        <div>
                            <span class="stats-label">Total Users</span>
                            <h3 class="stats-value"><?= $totalUsers ?></h3>
                        </div>
                    </div>
                    
                    <div class="card stats-card">
                        <i class="fa-solid fa-user-tie fa-2x icon-green"></i>
                        <div>
                            <span class="stats-label">Clients</span>
                            <h3 class="stats-value"><?= $totalClients ?></h3>
                        </div>
                    </div>
                    
                    <div class="card stats-card">
                        <i class="fa-solid fa-id-badge fa-2x icon-amber"></i>
                        <div>
                            <span class="stats-label">Staff</span>
                            <h3 class="stats-value"><?= $totalStaff ?></h3>
                        </div>
                    </div>
                </div>
                
                <!-- 1. System Users Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">System Users</h2>
                        <a href="../staff/processes/addUser.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add User
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Real Name</th>
                                    <th>User Type</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT UserID, Username, Email, RealName, UserType FROM user";
                                $result = mysqli_query($conn, $sql);

                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                        $userID   = htmlspecialchars($row['UserID']);
                                        $username = htmlspecialchars($row['Username']);
                                        $email    = htmlspecialchars($row['Email']);
                                        $realName = htmlspecialchars($row['RealName']);
                                        $userType = htmlspecialchars($row['UserType']);
                                ?>
                                        <tr>
                                            <td><?= $userID ?></td>
                                            <td><strong><?= $username ?></strong></td>
                                            <td><?= $email ?></td>
                                            <td><?= $realName ?></td>
                                            <td><span class="badge-usertype"><?= $userType ?></span></td>
                                            <td style="text-align: right;">
                                                <div class="actions-cell" style="justify-content: flex-end;">
                                                    <a href="../staff/processes/updateUser.php?updateID=<?= $userID ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                                                    <a href="../staff/processes/deleteUser.php?deleteID=<?= $userID ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fa-solid fa-trash"></i> Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <tr><td colspan="6" style="text-align:center;">No users found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Clients Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Clients</h2>
                        <a href="../staff/processes/addClient.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Client
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Address</th>
                                    <th>Country</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM client";
                                $result = mysqli_query($conn, $sql);

                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                        $userID  = htmlspecialchars($row['UserID']);
                                        $address = htmlspecialchars($row['Address']);
                                        $country = htmlspecialchars($row['Country']);
                                ?>
                                        <tr>
                                            <td><?= $userID ?></td>
                                            <td><?= $address ?></td>
                                            <td><?= $country ?></td>
                                            <td style="text-align: right;">
                                                <div class="actions-cell" style="justify-content: flex-end;">
                                                    <a href="../staff/processes/updateClient.php?updateID=<?= $userID ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                                                    <a href="../staff/processes/deleteClient.php?deleteID=<?= $userID ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fa-solid fa-trash"></i> Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <tr><td colspan="4" style="text-align:center;">No client records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Staff Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Staff Members</h2>
                        <a href="../staff/processes/addStaff.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Staff
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Employ Date</th>
                                    <th>Salary</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM staff";
                                $result = mysqli_query($conn, $sql);

                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                        $userID     = htmlspecialchars($row['UserID']);
                                        $employDate = htmlspecialchars($row['EmployDate']);
                                        $salary     = htmlspecialchars($row['Salary']);
                                ?>
                                        <tr>
                                            <td><?= $userID ?></td>
                                            <td><?= $employDate ?></td>
                                            <td><strong>RM <?= number_format((float)$salary, 2) ?></strong></td>
                                            <td style="text-align: right;">
                                                <div class="actions-cell" style="justify-content: flex-end;">
                                                    <a href="../staff/processes/updateStaff.php?updateID=<?= $userID ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                                                    <a href="../staff/processes/deleteStaff.php?deleteID=<?= $userID ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fa-solid fa-trash"></i> Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <tr><td colspan="4" style="text-align:center;">No staff records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>