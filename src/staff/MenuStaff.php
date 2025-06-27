<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php
	include(__DIR__ . '/../dbConnect.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>TreePacific</title>
	<link rel="icon" type="image/x-icon" href="/tree/public/img/tree.PNG">
	<style type="text/css">
		.header{
			display: flex;
			padding: 5px;
			background-color: #36454f;
			align-items: top;
			font-family: 'Trebuchet MS', sans-serif;
		}

		ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333;
        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        li a:hover:not(.active) {
            background-color: #04AA6D;
        }

        .active {
            background-color: #04AA6D;
        }

		.logo img{
			width: 65px;
			padding-left: 100px; 
			padding-right: 5px;
		}

		.logotext h1{
			padding-left: 5px;
			padding-right: 5px;
			font-size: 25px;
			color: #128C7E;
		}

		.logintext h1{
			padding-left: 5px;
			font-size: 25px;
		}

		body 
		{ 
			background-color: #075E54;
			margin: 0;
		}
		
		input[type=submit] {
			background-color: #c45b56;
			color: #ecd846;
			font-family: Helvetica;
		}

		.context {
			height: 600px;
			align-items: center;
			display: flex;
		}

		.contextimg img{
			width: 300px;
			position: relative;
			left: 270px;
		
		}

		figcaption{
			position: relative;
			left: 270px;
			color: white;
			font-size: 20px;
			font-family: 'Trebuchet MS', sans-serif;
			text-align: center;

		}

		.container {
			background-color: white;
			padding-left: 20px;
			padding-right: 20px;
			border-radius: 12px;
			width: 9cm;
			height: 250px;
			position: absolute;
			right: 250px;			
		}

		.form p::first-letter{
			font-size: 200%;
            color: #128C7E;
		}
		
		.container button{
			display: block;
			border-radius: 12px;
			font-size: 12px;
			width: 7.5cm;
			padding: 10px ;
			margin-right: auto;
			margin-left: auto;
			background-color: #128C7E;
			font-family: 'Trebuchet MS', sans-serif;
			color: white;
			width: 7.5cm;
		}
		
		.container a {
			text-decoration: none;
			font-family: 'Trebuchet MS', sans-serif;
			color: white;
			width: 7.5cm;
		}

		.formheader{
			font-family: 'Trebuchet MS', sans-serif;
			font-size: 20px;
		}

		.form input{
			display: block;
			border-radius: 12px;
			font-size: 12px;
			width: 7.5cm;
			padding: 10px ;
			margin-right: auto;
			margin-left: auto;
		}

		.submit{
			background-color: #128C7E;
			font-family: 'Trebuchet MS', sans-serif;
			color: white;
			width: 7.5cm;
		}

		.formfooter {
			font-family: 'Trebuchet MS', sans-serif;
			text-align: center;
			font-size: 13px;
		}

		.formfooter p{
			color: grey;
		}

		.formfooter a:link{
			color: #128C7E;
			text-decoration: none;
			font-weight: bold;
		}

		.formfooter a:visited{
			color: #128C7E;
			text-decoration: none;
			font-weight: bold;
		}

		.footer{
			background-color: #36454f;
			text-align: center;
			height: 90px;
			color: white;
			font-size: 12px;
		}

		.footer hr{
			width: 80%;
		}

	</style>
</head>
<body>
	<div class = "header">
		<div class="logo">
			<img src = "/tree/public/img/tree.PNG" alt="logo" >
		</div>
		<div class="logotext">
			<h1>TreePacific</h1>
		</div>
		<div class="logintext"> 
			<h1>Homepage</h1>
		</div>
	</div>

	<div class = "navbar">
	<ul>
        <li><a class = "active" href = "MenuStaff.php"> Homepage</a></li>
        <li><a href = "viewUser.php"> User</a></li>
        <li><a href = "viewCompany.php"> Companies </a></li>
        <li><a href = "viewTree.php"> Trees</a></li>
        <li><a href = "viewBlock.php"> Blocks </a></li>
        <li><a href = "viewOrchard.php"> Orchards</a></li>
        <li><a href = "viewSale.php"> Sales </a></li>
        <li style = "float: right"><a href="Logout.php">Log Out</a></li>
    </ul>
	</div>

 	<div class="context">
		<div class="contextimg">
			<figure>
			<img src = "/tree/public/img/tree.PNG" alt="TreePacificlogo">
			<figcaption><h3>TreePacific</h3>Tree Profiling Management System</figcaption>
			</figure>
		</div>
		<div class="container">
			<div class="formheader">
				<h3><span style = "color: #128C7E;">TreePacific </span>| About</h3>
			</div>
			<div class = "form">
				<hr>
				<p style = "text-align: justify">
				It costs 38 trillion dollars to produce oxygen for all humans on the planet for six months. This indicates that even if we spent all of the money in the world, we would be unable to provide oxygen for all humans for six months. Trees do it for free.
				</p>
			
			</div>
		</div>
	</div>
	<div class="footer">
		<br>
		<hr>
		<p>&copy; 2022 TREEPACIFIC. ALL RIGHTS RESERVED</p>
	</div>
</body>
</html>