<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php
include(__DIR__ . '/../dbConnect.php');

session_start();
$user_check=$_SESSION['login_user'];
$ses_sql=mysqli_query($conn,"SELECT Username,UserID from user where UserID='$user_check'");
$row=mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
$loggedin_session=$row['Username'];
$loggedin_id=$row['UserID'];
if(!isset($loggedin_session) || $loggedin_session==NULL) {
	echo "Go back";
	header("Location: LoginClient.php");
}
?>
