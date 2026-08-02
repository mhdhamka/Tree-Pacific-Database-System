<?php 
include(__DIR__ . '../../../config/dbConnect.php');
?>

<?php
    $count = 0;

    if(isset($_POST['reassignBlock']))
    {   
        $updateID = $_POST['updateID'];
        $saleID = $_GET['saleID'];
        $blockID = $_GET['blockID'];
        
        $sql = "SELECT * FROM sale";
        $result = mysqli_query($conn, $sql);

        if ($result && $result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                $count = $row['SaleID'];
                $count++;
            }
        }
        
        $sql = "SELECT BasePrice FROM block WHERE BlockID = '$blockID'";
        $result = mysqli_query($conn, $sql);

        if ($result && $result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {
                $price = $row['BasePrice'];
            }
        }
        
        $sql = "INSERT INTO sale(SaleID, ClientID, TotalPrice, DateSold)
                VALUES('$count', '$updateID', '$price', now())";
        $result = mysqli_query($conn, $sql);
                
        $sql = "INSERT INTO purchase(SaleID, BlockID, SellingPrice)
                VALUES('$count', '$blockID', '$price')";        
                
        if (mysqli_query($conn, $sql))
        {   
            $success = false;
            header("Location:viewSale.php");
            exit();
        }
        else
        {
            $errorMsg = "Error: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Reassign Block</title>
    
    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/59805f286a.js"></script>
    
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
        <?php include(__DIR__ . '../../../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Sales & Clients Commercial Operations</h1>
            </header>

            <div class="content-body">
                <!-- Breadcrumb Navigation -->
                <nav>
                    <ul class="breadcrumb">
                        <li><a href="../../views/staff/sales.php">Sales & Clients</a></li>
                        <li class="separator"><i class="fa fa-angle-right"></i></li>
                        <li class="active">Reassign Block</li>
                    </ul>
                </nav>

                <?php if (isset($errorMsg)): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>

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

                                        if ($result && $result->num_rows > 0)
                                        {
                                            while ($row = $result->fetch_assoc())
                                            {
                                                $blockID = htmlspecialchars($row['BlockID']);
                                                $baseprice = htmlspecialchars($row['BasePrice']);
                                                $orchardID = htmlspecialchars($row['OrchardID']);
                                                $saleID = htmlspecialchars($row['SaleID']);
                                                $userID = htmlspecialchars($row['UserID']);
                                                $realname = htmlspecialchars($row['RealName']);

                                                echo "<tr>
                                                        <td><span class='badge-block'>#".$blockID."</span></td>
                                                        <td><span class='price-text'>RM ".$baseprice."</span></td>
                                                        <td>".$orchardID."</td>
                                                        <td>#".$saleID."</td>
                                                        <td>#".$userID."</td>
                                                        <td class='company-name'>".$realname."</td>
                                                        <td>
                                                            <a class='btn-assign' href='assignBlock.php?updateID={$row['UserID']}&saleID={$row['SaleID']}&blockID={$row['BlockID']}'>
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
                        <form method="POST">
                            <div class="form-group">
                                <label for="updateID">User ID</label>
                                <div class="input-wrapper">
                                    <i class="fa fa-user-circle"></i>
                                    <input type="text" 
                                           class="form-control-input" 
                                           name="updateID" 
                                           id="updateID"
                                           value="<?php 
                                                if (isset($_GET['updateID'])) {
                                                    $updateID = mysqli_real_escape_string($conn, $_GET['updateID']);
                                                    $sql = "SELECT UserID FROM client WHERE UserID = '$updateID';";
                                                    $result = mysqli_query($conn, $sql);
                                                    
                                                    if ($result && $row = $result->fetch_assoc()) {
                                                        echo htmlspecialchars($row["UserID"]);
                                                    }
                                                }
                                           ?>" 
                                           placeholder="Enter User ID">
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
            <?php include(__DIR__ . '../../../includes/footer.php'); ?>
        </main>
    </div>
</body>
</html>