<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<?php 
	
	include(__DIR__ . '/../dbConnect.php');// connection to database

	session_start(); // starting the session, necessary for using session variables
	$Userid=$_POST['clientid'];
	$result = mysqli_query($con,"SELECT * FROM user WHERE UserID='$Userid'");
	$num_rows = mysqli_num_rows($result);
	if ($num_rows) 
	{
 		header("location: SignUpClient.php?remarks=failed"); 
	}
	else 
		{
 			$Userid=$_POST['clientid'];
 			$Username=$_POST['username'];
 			$Email=$_POST['email'];
	 		$PasswordHash=$_POST['password'];
	 		$Realname=$_POST['realname'];
	 		$Usertype= "C";
	 		$Address=$_POST['address'];
	 		$Country=$_POST['country'];

 		if(mysqli_query($con,"INSERT INTO user( UserID, Username, Email, PasswordHash, RealName, UserType) VALUES ('$Userid', '$Username','$Email', '$PasswordHash', '$Realname', '$Usertype')"))
 		{
 			$result = mysqli_query($con,"SELECT * FROM client WHERE UserID='$Userid'");
 			$num_rows = mysqli_num_rows($result);
 			if (!$num_rows)
 			{
 				if(mysqli_query($con,"INSERT INTO client( UserType, UserID, Address, Country )VALUES('$Usertype','$Userid', '$Address' , '$Country')"))
	 			{ 
					header("location: LoginClient.php?remarks=success");
		 		}
		 		else
		 		{
					$e=mysqli_error($con);
					header("location: SignUpClient.php?remarks=error&value=$e");	 
		 		}
 			}
 			
	 	}else 
	 	{
	 		$e=mysqli_error($con);
				header("location: SignUpClient.php?remarks=error&value=$e");
			}
	 	}
	?>	

/*	$result2 = mysqli_query($con,"SELECT * FROM client WHERE UserID='$Userid'");
	$num_rows2 = mysqli_num_rows($result2);
	if ($num_rows2) 
	{
 		header("location: SignUpClient.php?remarks=failed"); 
	}
	else 
		{
 			$Userid=$_POST['clientid'];
	 		$Usertype= "Client";

 		if(mysqli_query($con,"INSERT INTO client( UserType, UserID )VALUES('$Usertype','$Userid' )"))
 			{ 
				header("location: LoginClient.php?remarks=success");
	 		}
	 		else
	 		{
				$e=mysqli_error($con);
				header("location: SignUpClient.php?remarks=error&value=$e");	 
	 		}
		}

*/