<?php 
include(__DIR__ . '/../../config/dbConnect.php');

$saleID = isset($_GET['saleID']) ? htmlspecialchars($_GET['saleID']) : '';
$blockID = isset($_GET['blockID']) ? htmlspecialchars($_GET['blockID']) : '';
$updateID = isset($_GET['updateID']) ? htmlspecialchars($_GET['updateID']) : '';
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Reassign Block</title>
    
    <!-- FontAwesome & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../../assets/images/TREE.PNG">

    <link rel="stylesheet" href="../../../assets/css/staff.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include(__DIR__ . '/../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Sales & Clients Commercial Operations</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <nav class="breadcrumb">
                    <a href="../../views/staff/sales.php">Sales & Clients</a>
                    <span class="separator"><i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i></span>
                    <span class="active">Reassign Block</span>
                </nav>

                <!-- Table Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Select Block to Reassign</h2>
                    </div>
                    <div class="card-body" style="padding: 0;">
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM block
                                                INNER JOIN purchase ON block.BlockID = purchase.BlockID
                                                INNER JOIN sale ON purchase.SaleID = sale.SaleID
                                                INNER JOIN client ON sale.ClientID = client.UserID
                                                INNER JOIN user ON client.UserID = user.UserID";
                                
                                        $result = mysqli_query($conn, $sql);

                                        if ($result && mysqli_num_rows($result) > 0)
                                        {
                                            while ($row = mysqli_fetch_assoc($result))
                                            {
                                                $rowBlockID = htmlspecialchars($row['BlockID']);
                                                $baseprice = htmlspecialchars($row['BasePrice']);
                                                $orchardID = htmlspecialchars($row['OrchardID']);
                                                $rowSaleID = htmlspecialchars($row['SaleID']);
                                                $userID = htmlspecialchars($row['UserID']);
                                                $realname = htmlspecialchars($row['RealName']);

                                                echo "<tr>
                                                        <td><span class='badge-block'>#".$rowBlockID."</span></td>
                                                        <td><span class='price-text'>RM ".$baseprice."</span></td>
                                                        <td>".$orchardID."</td>
                                                        <td>#".$rowSaleID."</td>
                                                        <td>#".$userID."</td>
                                                        <td class='company-name'>".$realname."</td>
                                                        <td>
                                                            <a class='btn-assign' href='reassignBlock.php?updateID={$row['UserID']}&saleID={$row['SaleID']}&blockID={$row['BlockID']}'>
                                                                <i class='fa fa-exchange'></i> Reassign
                                                            </a>
                                                        </td>
                                                      </tr>";
                                            }
                                        }
                                        else
                                        {
                                            echo "<tr><td colspan='7' style='text-align: center; color: var(--text-muted);'>No record data available.</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="card" style="max-width: 500px;">
                    <div class="card-header">
                        <h2 class="card-title">Reassign Details</h2>
                    </div>
                    <div class="card-body">
                        <!-- Directed to salesController.php with GET parameters for block assignment -->
                        <form action="../../controllers/staff/salesController.php?blockID=<?php echo $blockID; ?>&saleID=<?php echo $saleID; ?>" method="POST">
                            <div class="form-group">
                                <label for="updateID">New User ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-user-circle"></i>
                                    <input type="text" 
                                           class="form-control-input" 
                                           name="updateID" 
                                           id="updateID"
                                           value="<?php echo $updateID; ?>" 
                                           placeholder="Enter User ID" required>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="reassignBlock" class="btn btn-primary">
                                    <i class="fa fa-check"></i> Update Block
                                </button>
                                <a href="../../views/staff/sales.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include(__DIR__ . '/../../includes/footer.php'); ?>
        </main>
    </div>
</body>
</html>