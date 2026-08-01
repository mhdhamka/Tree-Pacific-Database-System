
<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PacificTree | Sales</title>

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
                
                <!-- Sales & Block Assignments Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Sales & Block Assignments</h2>
                        <a href="assignBlock.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Assign Block
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Block ID</th>
                                    <th>Base Price</th>
                                    <th>Orchard ID</th>
                                    <th>Sale ID</th>
                                    <th>Client ID</th>
                                    <th>Client Name</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM block
                                            INNER JOIN purchase ON block.BlockID = purchase.BlockID
                                            INNER JOIN sale ON purchase.SaleID = sale.SaleID
                                            INNER JOIN client ON sale.ClientID = client.UserID
                                            INNER JOIN user ON client.UserID = user.UserID";
                    
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $blockID = htmlspecialchars($row['BlockID']);
                                            $baseprice = is_numeric($row['BasePrice']) ? 'RM ' . number_format($row['BasePrice'], 2) : htmlspecialchars($row['BasePrice']);
                                            $orchardID = htmlspecialchars($row['OrchardID']);
                                            $saleID = htmlspecialchars($row['SaleID']);
                                            $userID = htmlspecialchars($row['UserID']);
                                            $realname = htmlspecialchars($row['RealName']);

                                            echo "<tr>
                                                    <td><span class='badge-chip'>{$blockID}</span></td>
                                                    <td><span class='price-text'>{$baseprice}</span></td>
                                                    <td>{$orchardID}</td>
                                                    <td><span class='badge-chip'>{$saleID}</span></td>
                                                    <td>{$userID}</td>
                                                    <td><strong>{$realname}</strong></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='reassignBlock.php?updateID={$userID}&saleID={$saleID}&blockID={$blockID}' class='btn btn-outline-primary btn-sm'>
                                                                <i class='fa-solid fa-arrows-rotate'></i> Reassign
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' style='text-align:center;'>No sales records found.</td></tr>";
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