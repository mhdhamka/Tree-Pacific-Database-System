<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php
include(__DIR__ . '/../dbConnect.php');
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST") {
	$Username=mysqli_real_escape_string($con,$_POST['username']);
	$PasswordHash=mysqli_real_escape_string($con,$_POST['password']);
	$result = mysqli_query($con,"SELECT * FROM user");
	$c_rows = mysqli_num_rows($result);
	if ($c_rows!=$Username) {
		header("location: LoginClient.php?remark_login=failed");
	}
	$sql="SELECT UserID FROM user WHERE Username='$Username' and PasswordHash='$PasswordHash'";
	$result=mysqli_query($con,$sql);
	$row=mysqli_fetch_array($result);
	$active=$row['active'];
	$count=mysqli_num_rows($result);
	if($count==1) {
		$_SESSION['login_user']=$Username;
		header("location: ProfileClient.php");
	}
}
?>