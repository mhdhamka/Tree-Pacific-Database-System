<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php
	
include(__DIR__ . '/../dbConnect.php');

session_start();
if($_SERVER["REQUEST_METHOD"] == "POST") {
	$Userid=mysqli_real_escape_string($conn,$_POST['userid']);
	$PasswordHash=mysqli_real_escape_string($conn,$_POST['password']);
	$result = mysqli_query($conn,"SELECT * FROM user");
	$c_rows = mysqli_num_rows($result);
	if ($c_rows!=$Userid) {
		header("location: LoginClient.php?remark_login=failed");
	}
	$sql="SELECT Username FROM user WHERE UserID='$Userid' AND PasswordHash='$PasswordHash' AND UserType = 'C'";
	$result=mysqli_query($conn,$sql);
	$row=mysqli_fetch_array($result);
	$active=$row['active'];
	$count=mysqli_num_rows($result);
	if($count==1) {
		$_SESSION['login_user']=$Userid;
		header("location: MenuClient.php");
	}
}

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
			height: 100%;
			background-color: #075E54;
			margin: 0;
		}
		
		input[type=submit] {
			background-color: #c45b56;
			color: #ecd846;
			font-family: Helvetica;
		}

		.context {
			height: 805px;
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
			height: 370px;
			position: absolute;
			right: 400px;			
		}
		
		.container button{
			display: block;
			border-radius: 12px;
			font-size: 12px;
			width: 7.5cm;
			padding: 10px ;
			margin-right: auto;
			margin-left: auto;
			background-color: rgba(196, 91, 86, 0.7);
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

		.form .submit{
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
	<div class="header">
		<div class="logo">
			<img src = "/tree/public/img/tree.PNG" alt="logo" >
		</div>
		<div class="logotext">
			<h1>TreePacific</h1>
		</div>
		<div class="logintext"> 
			<h1>Log In</h1>
		</div>
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
				<h3><span style = "color: #128C7E;">Client </span>| Log In</h3>
			</div>
			<form class="form" method="POST" action="<?php ($_SERVER['PHP_SELF']) ?>">
				<br>
				<input type="text" placeholder="Userid" id="userid" name="userid" required>
				<br>
				<input type="password" placeholder="PasswordHash" id="password" name="password" required>
				<br>
				<input type="submit" value="LOG IN" class="submit" name="submit">
				<br>
			</form>
			<button style = "background-color: #25D366;"><a href="OptionLogin.php">CHANGE USER TYPE</a></button>
			<br>
			<hr>
			<div class="formfooter">
				<p>New to TreePacific?
				<a href="SignUpClient.php">Sign Up</a>
				</p>
			</div>
		</div>
	</div>
	<div class="footer">
		<br>
		<hr>
		<p>&copy; 2021 TREEPACIFIC. ALL RIGHTS RESERVED</p>
	</div>
</body>
</html>