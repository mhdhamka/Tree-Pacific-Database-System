<?php 

include(__DIR__ . '../../config/dbConnect.php');

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific | Reports</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../assets/images/TREE.PNG">
    
    <link rel="stylesheet" href="../../assets/css/staff.css">
    <style>
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .report-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .report-card .card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex-grow: 1;
        }

        .report-card .report-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 8px;
        }

        .report-card .report-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .report-card .report-desc {
            font-size: 0.875rem;
            color: var(--secondary);
            line-height: 1.5;
        }

        .report-card .card-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
        }

        .report-card .btn {
            width: 100%;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <?php include(__DIR__ . '../includes/sidebar.php'); ?>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="topbar">
                <h1 class="topbar-title">Tree Profiling Management System</h1>
            </header>

            <div class="content-body">
                <!-- Page Title Header Card -->
                <section class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-chart-pie" style="color: var(--primary); margin-right: 8px;"></i> System Reports</h2>
                    </div>
                </section>

                <!-- Reports Grid Selection -->
                <div class="report-grid">
                    
                    <!-- Report Item 1 -->
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="report-icon">
                                <i class="fa-solid fa-tree"></i>
                            </div>
                            <h3 class="report-title">Tree Blocks by Clients</h3>
                            <p class="report-desc">Generate detailed reports of tree blocks filtered according to specific clients.</p>
                        </div>
                        <div class="card-footer">
                            <a href="reportblock.php" class="btn btn-primary">
                                Generate Report <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Report Item 2 -->
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="report-icon">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <h3 class="report-title">Orchards by Companies</h3>
                            <p class="report-desc">View tree orchards categorized and grouped by registered company entities.</p>
                        </div>
                        <div class="card-footer">
                            <a href="reportorchard.php" class="btn btn-primary">
                                Generate Report <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Report Item 3 -->
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="report-icon">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <h3 class="report-title">Trees by Companies</h3>
                            <p class="report-desc">Comprehensive listing of all registered trees mapped across different companies.</p>
                        </div>
                        <div class="card-footer">
                            <a href="reporttree.php" class="btn btn-primary">
                                Generate Report <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Report Item 4 -->
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="report-icon">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <h3 class="report-title">Trees by Planting Dates</h3>
                            <p class="report-desc">Analyze and generate reports of tree profiles sorted according to their planting dates.</p>
                        </div>
                        <div class="card-footer">
                            <a href="reporttreedates.php" class="btn btn-primary">
                                Generate Report <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
        	<?php include(__DIR__ . '../includes/footer.php'); ?>

        </main>
    </div>
</body>
</html>