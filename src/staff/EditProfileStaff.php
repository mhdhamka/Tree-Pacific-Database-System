<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php 

	include('SessionServer.php');

	if($_SERVER["REQUEST_METHOD"] == "POST") {
	$Username=mysqli_real_escape_string($conn,$_POST['username']);
	$PasswordHash=mysqli_real_escape_string($conn,$_POST['password']);
	$Userid=mysqli_real_escape_string($conn,$_POST['clientid']);
 	$Email=mysqli_real_escape_string($conn,$_POST['email']);
	$Realname=mysqli_real_escape_string($conn,$_POST['realname']);
	$Adress=mysqli_real_escape_string($conn,$_POST['address']);
	$Country=mysqli_real_escape_string($conn,$_POST['country']);

	$sql1 = "UPDATE user SET UserID = '$Userid' , Email = '$Email', RealName = '$Realname', PasswordHash = '$PasswordHash' WHERE Username = '{$_SESSION['login_user']}'";
	//$sql2 = "UPDATE client SET Adress = '$Address', Country = '$Country' WHERE UserID = '{$_SESSION["UserID"]}'";
    $result1 = mysqli_query($conn,$sql1);
    //$result2 = mysqli_query($conn,$sql2);

    if ($result1) {
    echo "<script>alert('Profile has been updated successfully.');</script>";
    header("location: ProfileClient.php?remarks=success");
    }        			
	else {
	echo "<script>alert('Profile has not updated successfully.');</script>";
	header("location: EditProfileClient.php?remarks=error&value=$e");
	echo $conn->error;
	}
}

 ?>

<!DOCTYPE HTML>
<html lang = "en">

<head>
	<meta charset = "UTF-8">
	<title>TreePacific | Home</title>
	<link rel="icon" type="image/x-icon" href="tree.PNG">

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
			background-color: #7a3835;
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

		.content{
			background-color: rgba(245,245,245,255);
			padding: 20px;
		}

		.container{
			padding: 10px;
			background-color: lightgrey;
			width: 60%;
			margin-left: auto;
			margin-right:auto;
			padding: 20px;
			border: 3px solid rgb(237,237,237);
			font-family: Helvetica;
		}

		.container button{
			display: block;
			border-radius: 12px;
			font-size: 12px;
			width: 7.5cm;
			padding: 5px ;
			margin-right: auto;
			margin-left: auto;
			font-family: 'Trebuchet MS', sans-serif;
			background-color: #36454f
			color: black;
		}

		.submit{
			display: block;
			border-radius: 15px;
			font-size: 12px;
			padding: 12px ;
			margin-right: auto;
			margin-left: auto;
			background-color: rgb(18, 140, 126);
			font-family: 'Trebuchet MS', sans-serif;
			font-weight: bold;
			color: white;
			width: 6cm;
		}

		.cancel{
			display: block;
			border-radius: 15px;
			font-size: 12px;
			padding: 12px ;
			margin-right: auto;
			margin-left: auto;
			background-color: rgb(18, 140, 126);
			font-family: 'Trebuchet MS', sans-serif;
			font-weight: bold;
			color: white;
			width: 6cm;
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
			border-radius: 15px;
			font-size: 12px;
			width: 15cm;
			padding: 12px ;
			margin-right: auto;
			margin-left: auto;
		}

		.form{
			font-family: Helvetica;
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
			border: 2px solid #ecd846;
			background: #c45b56;
		}
		
		.main .display-accounts .addbutton button a {
			color: #ecd846;
		}
		
		.main table {
			width: 100%;
			border: 1px solid black;
		}
		
		.main thead, tbody, th, tr {
			border: 1px solid black;
			background: #c45b56; 
			color: #ecd846;
		}
	</style>
	
	<script src="https://use.fontawesome.com/59805f286a.js"></script>
</head>

<body>
	<div class = "sidebarcontainer">
		<div class = "sidebar" id = "sidebar">
			<ul>
				<li>
					<a href = "">
						<span class = "icon"><img src = "tree.PNG" width = "50px"></span>
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
					<a href = "SaleClient.php">
						<span class = "icon"><i class = "fa fa-shopping-cart"></i></span>
						<span class = "title">Purchase</span>
					</a>
				</li>
				<li>
					<a href = "OptionLogin.php">
						<span class = "icon"><i class = "fa fa-sign-out"></i></span>
						<span class = "title">Log Out</span>
					</a>
				</li>
			</ul>
		</div>
		
		<div class = "main">
			<div class = "topbar">
				<div class = "admin">
					<i style = "color: #128C7E" class = "fa fa-user-circle"></i>
					<small>
                    <?php echo $loggedin_session; ?>
					</small>
				</div>
			</div>
			<?php
	$sql1="SELECT * FROM user where UserID=$loggedin_id";
	//$sql2="SELECT * FROM client ";
	$result1=mysqli_query($conn,$sql1);
	//$result2=mysqli_query($conn,$sql2);
	?>
	<?php
	while($rows=mysqli_fetch_array($result1)){
	?>		
	<div class="content">
	<div class="container">
	        <div class="formheader">
				<h3><span style = "color: #128C7E;">Client </span>| Edit Profile</h3>
			</div>		
		<form class="form" method="POST" action="<?php ($_SERVER['PHP_SELF']) ?>">
			<br>
				UserID: <p> <?php echo $rows['UserID']; ?>" 			
			<br>
				Username: <input type="text" value="<?php echo $rows['Username']; ?>" id="username" name="username" >
			<br>
				Email: <input type="text" value="<?php echo $rows['Email']; ?>" id="email" name="email" >
			<br>
				Fullname: <input type="text" value="<?php echo $rows['RealName']; ?>" id="realname" name="realname" >
			<br>
				PasswordHash: <input type="text" value="<?php echo $rows['PasswordHash']; ?>" id="password" name="password" >
		    <br>
				<input type="submit" value="SAVE" class="submit" name="submit">
	    	<br>
		</form>	
	<?php 
	}// close while loop 
	?>		
	<a href = ClientProfile.php>
	<button style = color: black; background-color: #36454f><a href = "ProfileClient.php">Cancel</a></button>
    </a>

	</div>
	</div>	
			</div>
		</div>
	</div>
</body>
</html>