
<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Orchards</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include(__DIR__ . '../includes/sidebar.php'); ?>

        <!-- Main Content Layout -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Orchard Overview Card -->
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Orchards</h2>
                        <a href="addOrchard.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Add Orchard
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Orchard ID</th>
                                    <th>Address</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM orchard";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td><strong>".htmlspecialchars($row['OrchardID'])."</strong></td>
                                                    <td>".htmlspecialchars($row['Address'])."</td>
                                                    <td>".htmlspecialchars($row['Lattitude'])."</td>
                                                    <td>".htmlspecialchars($row['Longitude'])."</td>
                                                    <td>
                                                        <div class='actions-cell'>
                                                            <a href=\"updateOrchard.php?updateID=".urlencode($row['OrchardID'])."\" class='btn-action btn-update'>
                                                                <i class='fa-solid fa-pen'></i> Edit
                                                            </a>
                                                            <a href=\"deleteOrchard.php?deleteID=".urlencode($row['OrchardID'])."\" class='btn-action btn-delete'>
                                                                <i class='fa-solid fa-trash'></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' style='text-align: center; color: var(--secondary);'>No orchard records found.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Orchard Management History Card -->
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title">Orchard Management History</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Manage ID</th>
                                    <th>Orchard ID</th>
                                    <th>User ID</th>
                                    <th>Manage Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM managementhistory";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td><strong>".htmlspecialchars($row['ManageID'])."</strong></td>
                                                    <td>".htmlspecialchars($row['OrchardID'])."</td>
                                                    <td>".htmlspecialchars($row['StaffID'])."</td>
                                                    <td>".htmlspecialchars($row['ManageDate'])."</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' style='text-align: center; color: var(--secondary);'>No management history found.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>