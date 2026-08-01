

<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Blocks</title>

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
                
                <!-- Blocks Management Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Block List</h2>
                        <a href="addBlock.php" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Add Block
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Block ID</th>
                                    <th>Base Price</th>
                                    <th>Orchard ID</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    global $conn;
                                    $sql = "SELECT * FROM block";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $blockID = htmlspecialchars($row['BlockID']);
                                            $basePrice = is_numeric($row['BasePrice']) ? 'RM ' . number_format($row['BasePrice'], 2) : htmlspecialchars($row['BasePrice']);
                                            $orchardID = htmlspecialchars($row['OrchardID']);

                                            echo "<tr>
                                                    <td><span class='badge-chip'>{$blockID}</span></td>
                                                    <td><span class='price-text'>{$basePrice}</span></td>
                                                    <td><span class='badge-chip'>{$orchardID}</span></td>
                                                    <td style='text-align: right;'>
                                                        <div class='actions-cell' style='justify-content: flex-end;'>
                                                            <a href='updateBlock.php?updateID={$blockID}' class='btn btn-outline-primary btn-sm'>
                                                                <i class='fa-solid fa-pen-to-square'></i> Update
                                                            </a>
                                                            <a href='deleteBlock.php?deleteID={$blockID}' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this block?');\">
                                                                <i class='fa-solid fa-trash'></i> Delete
                                                            </a>
                                                        </div>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' style='text-align:center;'>No blocks found.</td></tr>";
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