<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php include('SessionServer.php'); ?>

<!DOCTYPE HTML>
<html lang = "en">

<head>
	<meta charset = "UTF-8">
	<title>TreePacific | Home</title>
	<link rel="icon" type="image/x-icon" href="/tree/public/img/tree.PNG">

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
			background-color: #36454f;
		}
		
		tbody {
			align-items: center;
		}
		
		.sidebarcontainer {
			position: relative;
			width: 100%;
		}
		
		.sidebar {
			position: fixed;
			width: 325px;
			height: 100%;
			background: #075E54;
			transition: 0.5s;
			overflow: hidden;
		}
		
		.sidebar a.active {
			background:  #36454f;
			color: #ecd846;
		}
		
		.sidebar ul li a.active .icon .fa {
			color: #128C7E;
			font-size: 24px;
		}
		
		.sidebar ul li a.active .title {
			position: relative;
			display: block;
			padding: 0 10px;
			height: 60px;
			line-height: 60px;
			color: #128C7E;
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
			background: #128C7E;
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
			color: #25D366;
			font-size: 24px;
		}
		
		.sidebar ul li a .title {
			position: relative;
			display: block;
			padding: 0 10px;
			height: 60px;
			line-height: 60px;
			color: #25D366;
			white-space: nowrap;
		}
		
		.main {
			position: absolute;
			width: calc(100% - 325px);
			left: 325px;
			min-height: 100vh;
			background: white;
		}
		
		.main .topbar {
			width: 100%;
			background: #36454f;
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
			color: #128C7E;
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
			color: #128C7E; 
		}
		
		.main .display-accounts .addbutton {
			padding: 20px 0 0 0;
		}
		
		.main .display-accounts .addbutton button {
			padding: 5px;
			border-radius: 5px;
			border: 2px solid 	#075E54;
			background: #128C7E;
		}
		
		.main .display-accounts .addbutton button a {
			color: white;
		}
		
		.main table {
			width: 100%;
			border: 2px solid #075E54;
		}
		
		.main thead, tbody, th, tr {
			border: 1px solid black;
			background: #128C7E; 
			color: #25D366;
		}
	</style>
	
	<script src="https://use.fontawesome.com/59805f286a.js"></script>
</head>

<body>
	<div class = "sidebarcontainer">
		<div class = "sidebar" id = "sidebar">
			<ul>
				<li>
					<a href = "MenuClient.php">
						<span class = "icon"><img src = "/tree/public/img/tree.PNG" width = "50px"></span>
						<span class = "title"><h2>TreePacific</h2></span>
					</a>
				</li>
				<li>
					<a class = "active" href = "ProfileClient.php">
						<span class = "icon"><i class = "fa fa-users"></i></span>
						<span class = "title">Accounts</span>
					</a>
				</li>
				<li>
					<a href = "PurchaseClient.php">
						<span class = "icon"><i class = "fa fa-shopping-cart"></i></span>
						<span class = "title">Purchase</span>
					</a>
				</li>
				<li>
					<a href = "Logout.php">
						<span class = "icon"><i class = "fa fa-sign-out"></i></span>
						<span class = "title">Log Out</span>
					</a>
				</li>
			</ul>
		</div>
		
		<div class = "main">
			<div class = "topbar">
				<div class = "admin">
					<i style = "color: #c45b56" class = "fa fa-user-circle"></i>
					<small>
                    <?php echo $loggedin_session; ?>
					</small>
				</div>
			</div>
			<?php
			$sql1="SELECT * FROM user where UserID=$loggedin_id";
			$sql2="SELECT * FROM client where UserID=$loggedin_id ";
			$result1=mysqli_query($conn,$sql1);
			$result2=mysqli_query($conn,$sql2);
	?>
	<?php
	while($rows=mysqli_fetch_array($result1)) {
	?>			
			<div class = "display-accounts">
				<ul>
					<li>
						<p  style = "font-weight: bold;">Accounts</>
					</li>
				</ul>
				
				<table>
					<thead>
						<tr>
							<th scope = "col">User ID</th>
							<th scope = "col">Username</th>
							<th scope = "col">Email</th>
                            <th scope = "col">Realname</th>
							<th scope = "col">PasswordHash</th>
							<th scope = "col">Address</th>
							<th scope = "col">Country</th>
							<th scope = "col">Profile photo</th>
						</tr>
					</thead>
					<tbody>
					<td><?php echo $rows['UserID']; ?></td>
					<td><?php echo $rows['Username']; ?></td>
					<td><?php echo $rows['Email']; ?></td>
					<td><?php echo $rows['RealName']; ?></td>
					<td><?php echo $rows['PasswordHash']; ?></td>
					
					<td><?php if($rows=mysqli_fetch_assoc($result2)) { echo $rows['Address']; ?></td>
					<td><?php echo $rows['Country']; ?></td>
					<td><?php echo '<img style="width: 100px; height: 100px" src="data:image;base64,'.base64_encode($rows['Photo']).'" alt="Image">'; }?></td>
					</tbody>
				</table>
	<?php 
	}// close while loop 
	?>		
				<div class = "addbutton">
					<button><a href = "EditProfileClient.php">Edit Account</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>