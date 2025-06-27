<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php 
	
	include(__DIR__ . '/../dbConnect.php');// connection to database
	
	session_start(); // starting the session, necessary for using session variables
	$Userid=$_POST['staffid'];
	$result = mysqli_query($conn,"SELECT * FROM user WHERE UserID='$Userid'");
	$num_rows = mysqli_num_rows($result);
	if ($num_rows) 
	{
 		header("location: SignUpStaff.php?remarks=failed"); 
	}
	else 
		{
 			$Userid=$_POST['staffid'];
 			$Username=$_POST['username'];
 			$Email=$_POST['email'];
	 		$PasswordHash=$_POST['password'];
	 		$Realname=$_POST['realname'];
	 		$Usertype= "S";

 		if(mysqli_query($conn,"INSERT INTO user( UserID, Username, Email, PasswordHash, RealName, UserType) VALUES ('$Userid', '$Username','$Email', '$PasswordHash', '$Realname', '$Usertype')"))
 		{
 			$result = mysqli_query($conn,"SELECT * FROM staff WHERE UserID='$Userid'");
 			$num_rows = mysqli_num_rows($result);
 			if (!$num_rows)
 			{
 				if(mysqli_query($conn,"INSERT INTO staff( UserID )VALUES('$Userid')"))
	 			{ 
					header("location: LoginStaff.php?remarks=success");
		 		}
		 		else
		 		{
					$e=mysqli_error($conn);
					header("location: SignUpStaff.php?remarks=error&value=$e");	 
		 		}
 			}
 			
	 	}else 
	 	{
	 		$e=mysqli_error($conn);
				header("location: SignUpStaff.php?remarks=error&value=$e");
			}
	 	}
	?>