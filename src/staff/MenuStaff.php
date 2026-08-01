<?php
    include(__DIR__ . '/../dbConnect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreePacific - Staff Dashboard</title>
    <link rel="icon" type="image/x-icon" href="/tree/public/img/tree.PNG">
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #128C7E;
            --primary-hover: #075E54;
            --accent: #04AA6D;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --radius: 16px;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* --- Header & Branding --- */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background-color: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 8px;
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.02em;
        }

        .page-badge {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        /* --- Navigation Bar --- */
        .navbar {
            background-color: #161f30;
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            overflow-x: auto;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 0.25rem;
            max-width: 1200px;
            margin: 0 auto;
            white-space: nowrap;
        }

        .navbar li a {
            display: inline-block;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .navbar li a:hover {
            color: var(--text-main);
            background-color: rgba(255, 255, 255, 0.03);
        }

        .navbar li a.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background-color: rgba(18, 140, 126, 0.08);
        }

        .navbar li.logout-item {
            margin-left: auto;
        }

        .navbar li.logout-item a:hover {
            color: #ef4444;
            border-bottom-color: transparent;
        }

        /* --- Main Content Area --- */
        .main-container {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            align-items: center;
        }

        /* Hero / Graphic Section */
        .hero-card {
            background: linear-gradient(135deg, rgba(18, 140, 126, 0.15) 0%, rgba(30, 41, 59, 0.5) 100%);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-card img {
            width: 140px;
            height: auto;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }

        .hero-card img:hover {
            transform: translateY(-4px);
        }

        .hero-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .hero-card p {
            font-size: 0.95rem;
            color: var(--primary);
            font-weight: 500;
        }

        /* Content Card / About Section */
        .info-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }

        .card-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .card-header h3 span {
            color: var(--primary);
        }

        .quote-content {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .quote-content::first-letter {
            font-size: 1.8em;
            font-weight: 700;
            color: var(--primary);
            float: left;
            line-height: 1;
            padding-right: 6px;
        }

        /* --- Footer --- */
        .footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            background-color: var(--bg-dark);
            margin-top: auto;
        }

        .footer p {
            font-size: 0.8rem;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }

        /* --- Responsive Design --- */
        @media (max-width: 850px) {
            .main-container {
                grid-template-columns: 1fr;
                padding: 2rem 1rem;
            }

            .header {
                padding: 1rem;
            }

            .navbar {
                padding: 0 0.5rem;
            }
            
            .navbar li a {
                padding: 0.75rem 0.6rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="brand">
            <img src="/tree/public/img/tree.PNG" alt="TreePacific Logo">
            <h1 class="brand-title">TreePacific</h1>
        </div>
        <span class="page-badge">Staff Dashboard</span>
    </header>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <ul>
            <li><a class="active" href="MenuStaff.php">Homepage</a></li>
            <li><a href="viewUser.php">User</a></li>
            <li><a href="viewCompany.php">Companies</a></li>
            <li><a href="viewTree.php">Trees</a></li>
            <li><a href="viewBlock.php">Blocks</a></li>
            <li><a href="viewOrchard.php">Orchards</a></li>
            <li><a href="viewSale.php">Sales</a></li>
            <li class="logout-item"><a href="Logout.php">Log Out</a></li>
        </ul>
    </nav>

    <!-- Main Content Grid -->
    <main class="main-container">
        
        <!-- System Hero Graphic -->
        <div class="hero-card">
            <img src="/tree/public/img/tree.PNG" alt="TreePacific Graphic">
            <h2>TreePacific</h2>
            <p>Tree Profiling Management System</p>
        </div>

        <!-- Information / About Box -->
        <article class="info-card">
            <div class="card-header">
                <h3><span>TreePacific</span> &mdash; About</h3>
            </div>
            <div class="quote-content">
                It costs 38 trillion dollars to produce oxygen for all humans on the planet for six months. This indicates that even if we spent all of the money in the world, we would be unable to provide oxygen for all humans for six months. Trees do it for free.
            </div>
        </article>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> TREEPACIFIC. ALL RIGHTS RESERVED.</p>
    </footer>

</body>
</html>