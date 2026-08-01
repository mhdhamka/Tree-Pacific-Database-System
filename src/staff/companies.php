<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Companies</title>

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
        <?php include(__DIR__ . '../includes/sidebar.php'); ?>

        <!-- Main Dashboard View -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                
                <!-- Companies Management Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Company List</h2>
                        <a href="addCompany.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Company
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>Company Orchard</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM company";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $companyID = htmlspecialchars($row['CompanyID']);
                                            $companyName = htmlspecialchars($row['CompanyName']);
                                            $orchardID = htmlspecialchars($row['OrchardID']);

                                            echo "<tr>
                                                    <td><span class='badge-chip'>{$companyID}</span></td>
                                                    <td><span class='company-name'>{$companyName}</span></td>
                                                    <td><span class='badge-chip'>{$orchardID}</span></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='updateCompany.php?updateID={$companyID}' class='btn btn-outline-primary btn-sm'>
                                                                <i class='fa-solid fa-pen-to-square'></i> Update
                                                            </a>
                                                            <a href='deleteCompany.php?deleteID={$companyID}' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this company?');\">
                                                                <i class='fa-solid fa-trash'></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' style='text-align:center;'>No companies found.</td></tr>";
                                    }

                                    if (isset($conn)) {
                                        mysqli_close($conn);
                                    }
                                ?>
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