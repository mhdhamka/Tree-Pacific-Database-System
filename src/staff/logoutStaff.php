<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php
	include(__DIR__ . '/../dbConnect.php');

	global $conn;
	$sql = "UPDATE user SET LogStatus = 0 WHERE LogStatus = 1";
	$result = mysqli_query($conn, $sql);

	header("Location:/tree/src/OptionLogin.php");
?>