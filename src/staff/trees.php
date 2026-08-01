<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Trees</title>
    
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
                
                <!-- 1. Trees Inventory Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Tree Records</h2>
                        <a href="addTree.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Tree
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tree ID</th>
                                    <th>Species Name</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Block ID</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM tree";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $treeID = htmlspecialchars($row['TreeID']);
                                            $speciesName = htmlspecialchars($row['SpeciesName']);
                                            $latitude = htmlspecialchars($row['Lattitude']);
                                            $longitude = htmlspecialchars($row['Longitude']);
                                            $blockID = htmlspecialchars($row['BlockID']);

                                            echo "<tr>
                                                    <td>{$treeID}</td>
                                                    <td><strong>{$speciesName}</strong></td>
                                                    <td>{$latitude}</td>
                                                    <td>{$longitude}</td>
                                                    <td><span class='badge-block'>{$blockID}</span></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='updateTree.php?updateID={$treeID}' class='btn btn-outline-primary btn-sm'><i class='fa-solid fa-pen'></i> Edit</a>
                                                            <a href='deleteTree.php?deleteID={$treeID}' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Are you sure?\");'><i class='fa-solid fa-trash'></i> Delete</a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' style='text-align:center;'>No tree records found.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Tree Updates Audit Log Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Tree Status Updates</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Update ID</th>
                                    <th>Tree ID</th>
                                    <th>Staff ID</th>
                                    <th>Tree Height</th>
                                    <th>Tree Diameter</th>
                                    <th>Tree Status</th>
                                    <th>Update Date</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM treeupdate";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $updateID = htmlspecialchars($row['UpdateID']);
                                            $treeID = htmlspecialchars($row['TreeID']);
                                            $staffID = htmlspecialchars($row['StaffID']);
                                            $treeHeight = htmlspecialchars($row['TreeHeight']);
                                            $treeDiameter = htmlspecialchars($row['TreeDiameter']);
                                            $treeStatus = htmlspecialchars($row['TreeStatus']);
                                            $updateDate = htmlspecialchars($row['UpdateDate']);

                                            echo "<tr>
                                                    <td>{$updateID}</td>
                                                    <td><strong>{$treeID}</strong></td>
                                                    <td>{$staffID}</td>
                                                    <td>{$treeHeight} m</td>
                                                    <td>{$treeDiameter} cm</td>
                                                    <td><span class='badge-status'>{$treeStatus}</span></td>
                                                    <td>{$updateDate}</td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='deleteTreeUpdate.php?deleteID={$updateID}' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this update log?\");'><i class='fa-solid fa-trash'></i> Delete</a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' style='text-align:center;'>No tree update logs found.</td></tr>";
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