<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php include(__DIR__ . '/../dbConnect.php');

	if(isset($_POST['addClient'])) {
		$userID = $_POST['userID'];
		$address = $_POST['address'];
		$country = $_POST['country'];
		$photo = $_POST['photo'];

		global $conn;
		
		$sql = "INSERT INTO client(UserID, Address, Country, Photo)
				VALUES('$userID', '$address', '$country', '$photo')";
				
		if (mysqli_query($conn, $sql))
		{   
			$success = false;
		}
		else
		{
			echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		}
	}
?>

<!DOCTYPE HTML>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>TreePacific | Home</title>

	<style>
		*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: Helvetica;
		}
		
		body {
			overflow-x: hidden;
		}
		
		table tr th {
			background-color: #859998;
		}
		
		tbody {
			align-items: center;
		}
		
		.container {
			position: relative;
			width: 100%;
		}
		
		.sidebar {
			position: fixed;
			width: 325px;
			height: 100%;
			background: #4c5c5a;
			transition: 0.5s;
			overflow: hidden;
		}
		
		.sidebar a.active {
			background:  #d6dbde;
			color: #4c5c5a;
		}
		
		.sidebar ul li a.active .icon .fa {
			color: #4c5c5a;
			font-size: 24px;
		}
		
		.sidebar ul li a.active .title {
			position: relative;
			display: block;
			padding: 0 10px;
			height: 60px;
			line-height: 60px;
			color: #4c5c5a;
			white-space: nowrap;
		}
		
		
		.sidebar ul {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
		}
		
		.sidebar ul li {
			position: relative;
			width: 100%;
			list-style: none;
		}

		
		.sidebar ul li:hover {
			background: #859998;
		}
		
		.sidebar ul li:nth-child(1) {
			margin-bottom: 10px;
		}
		
		.sidebar ul li:nth-child(1):hover {
			background: transparent;
		}
		
		.sidebar ul li a {
			position: relative;
			display: block;
			width: 100%;
			display: flex;
			text-decoration: none;
		}
		
		.sidebar ul li a .icon { 
			position: relative;
			display: block;
			min-width: 60px;
			height: 60px;
			line-height: 60px;
			text-align: center;
		}
		
		.sidebar ul li a .icon .fa {
			color: #d6dbde;
			font-size: 24px;
		}
		
		.sidebar ul li a .title {
			position: relative;
			display: block;
			padding: 0 10px;
			height: 60px;
			line-height: 60px;
			color: #d6dbde;
			white-space: nowrap;
		}
		
		.main {
			position: absolute;
			width: calc(100% - 325px);
			left: 325px;
			min-height: 100vh;
			background: lightgrey;
		}
		
		.main .topbar {
			width: 100%;
			background: white;
			height: 60px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 20px;
		}
		
		.main .topbar i {
			font-size: 25px;
		}
		
		.main .topbar small {
			visibility: visible;
			padding: 10px;
		}
		
		.main .form-control {
			padding: 20px;
		}
		
		.main ul {
			list-style-type: none;
			overflow: hidden;
			padding: 20px 20px 0 20px;
		}
		
		.main ul li {
			float: left;
			padding-right: 10px;
			color: #4c5c5a;
		}
		
		.main ul li a {
			display: block;
			color: #4c5c5a;
			text-decoration: none;
		}
		
		.main ul li p {
			display: block;
			color: #4c5c5a;
			text-decoration: none;
		}
		
		.main .display-accounts {
			padding: 20px;
		}
		
		.main .display-accounts ul {
			list-style: none;
			padding: 0 0 20px 0;
		}
		
		.main .display-accounts a {
			text-decoration: none;
			color: #4c5c5a; 
		}
		
		.form-control {
			padding-bottom: 20px;
		}
		
		.form-control i {
			color: #4c5c5a;
		}
		
		.form-control label {
			display: inline-block;
			margin-bottom: 5px;
		}
		
		.form-control input[type=text], input[type=email], input[type=password]{
			border: 2px solid darkgrey;
			border-radius: 5px;
			display: block;
			padding: 10px;
			width: 50%;
		}
		
		.form-control small{
			visibility: hidden;
		}
		
		.form-control input[type=submit] {
			background-color: #d6dbde;
			padding: 5px;
			border-radius: 4px;
			border: 2px solid #859998;
			color: #4c5c5a;
			width: 10%;
		}
		
		.form-control.error small{
			visibility: visible;
			color: #4c5c5a;
		}
		
		.form-control.success small{
			visibility: hidden;
		}
		
		.form-control.error input{
			border-color: #4c5c5a;
		}
		
		.form-control.success input{
			border-color: #4c5c5a;
		}
		
		.main table {
			width: 100%;
			border: 1px solid black;
		}
		
		.main thead, tbody, th, tr {
			border: 1px solid black;
			background: #d6dbde; 
			color: #4c5c5a;
		}
			
	</style>
	
	<script src="https://use.fontawesome.com/59805f286a.js"></script>

	<link rel="icon" type="image/x-icon" href="tree.png">
</head>

<body>
	<div class = "container">
		<div class = "sidebar" id = "sidebar">
			<ul>
				<li>
					<a>
						<span class = "icon"><i class = "fa fa-user-circle"></i></span>
						<span class = "icon" style = "color: #d6dbde">
							<?php
								global $conn;
								$sql = "SELECT Username FROM user WHERE logStatus = 1 && UserType = 'S';";
								$result = mysqli_query($conn, $sql);
								
								if ($result -> num_rows > 0)
								{
									while ($row = $result -> fetch_assoc())
									{
										echo $row["Username"];
									}
								}
							?>							
						</span>
					</a>
				</li>
				<li>
					<a class = "active" href = "viewUser.php">
						<span class = "icon"><i class = "fa fa-users"></i></span>
						<span class = "title">Users</span>
					</a>
				</li>
				<li>
					<a href = "viewCompany.php">
						<span class = "icon"><i class = "fa fa-building"></i></span>
						<span class = "title">Companies</span>
					</a>
				</li>
				<li>
					<a href = "viewTree.php">
						<span class = "icon"><i class = "fa fa-leaf"></i></span>
						<span class = "title">Trees</span>
					</a>
				</li>
				<li>
					<a href = "viewBlock.php">
						<span class = "icon"><i class = "fa fa-tree"></i></span>
						<span class = "title">Blocks</span>
					</a>
				</li>
				<li>
					<a href = "viewOrchard.php">
						<span class = "icon"><i class = "fa fa-map"></i></span>
						<span class = "title">Orchards</span>
					</a>
				</li>
				<li>
					<a href = "viewSale.php">
						<span class = "icon"><i class = "fa fa-line-chart"></i></span>
						<span class = "title">Sales</span>
					</a>
				</li>
				<li>
					<a href = "viewReport.php">
						<span class = "icon"><i class = "fa fa-table"></i></span>
						<span class = "title">Report</span>
					</a>
				</li>
				<li>
					<a href = "logoutStaff.php">
						<span class = "icon"><i class = "fa fa-sign-out"></i></span>
						<span class = "title">Log Out</span>
					</a>
				</li>
			</ul>
		</div>
		
		<div class = "main">
			<div class = "topbar">
				<div class = "admin">
					<h1 style = "color: #859998;">
						Tree Profiling Management System
					</h1>
				</div>
			</div>
			
			<div class = "display-accounts">
				<ul>
					<li>
						<a href = "viewUser.php">Users</a>
					</li>
					<li>
						<p> >> </p>
					</li>
					<li>
						<a href = "addClient.php" style = "font-weight: bold;">Add Client</a>
					</li>
				</ul>
				<ul>
					<li>
						<a href = "addClient.php" style = "font-weight: bold;">Users</a>
					</li>
				</ul>
				<table>
					<thead>
						<tr>
							<th scope = "col">User ID</th>
							<th scope = "col">Username</th>
							<th scope = "col">Email</th>
							<th scope = "col">PasswordHash</th>
							<th scope = "col">Real Name</th>
							<th scope = "col">User Type</th>
						</tr>
					</thead>
					<tbody>
						<?php
							global $conn;
							$sql = "SELECT * FROM user WHERE UserType = 'C'";
							$result = mysqli_query($conn, $sql);

							if ($result -> num_rows > 0)
							{
								while ($row = $result -> fetch_assoc())
								{
									echo "<tr style = 'text-align: center;'>
											<td>".$row['UserID']."</td>
											<td>".$row['Username']."</td>
											<td>".$row['Email']."</td>
											<td>".$row['PasswordHash']."</td>
											<td>".$row['RealName']."</td>
											<td>".$row['UserType']."</td>
										 </tr>";
								}
							}
						?>
					</tbody>
				</table>
				<br>
				<ul>
					<li>
						<a href = "addClient.php" style = "font-weight: bold;">Clients</a>
					</li>
				</ul>
				<table>
					<thead>
						<tr>
							<th scope = "col">User ID</th>
							<th scope = "col">Address</th>
							<th scope = "col">Country</th>
						</tr>
					</thead>
					<tbody>
						<?php
							global $conn;
							$sql = "SELECT * FROM client";
							$result = mysqli_query($conn, $sql);

							if ($result -> num_rows > 0)
							{
								while ($row = $result -> fetch_assoc())
								{
									echo "<tr style = 'text-align: center;'>
											<td>".$row['UserID']."</td>
											<td>".$row['Address']."</td>
											<td>".$row['Country']."</td>
										 </tr>";
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
			<form method = "POST">
				<div class = "form-control">
					<i class="fa fa-user-circle"></i>
					<label>User ID: </label>
					<input type = "text" name = "userID" id = "userID" required>
					<small>Invalid</small>
				</div>
				<div class = "form-control">
					<i class="fa fa-address-book"></i>
					<label>Address: </label>
					<input type = "text" name = "address" id = "address" required>
					<small>Invalid</small>
				</div>
				<div class = "form-control">
					<i class="fa fa-map-marker"></i>
					<label>Country: </label>
					<input type = "text" name = "country" id = "country" required>
					<small>Invalid</small>
				</div>
				<div class = "form-control">
					<i class="fa fa-file-image-o"></i>
					<label>Photo: </label>
					<input type = "file" name = "photo" id = "photo" required>
					<small>Invalid</small>
				</div>
				<div class = "form-control">
					<input type = "submit" name = "addClient" value = "Submit"></input>
				</div>
			</form>
		</div>
	</div>
</body>
</html>