

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
