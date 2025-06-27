<!---TMF2034: Database Concept & Design (G07)--->
<!---1. Mohammad Hamka Izzuddin Bin Mohamad Yahya (73571)--->
<!---2. Harith Zakwan Bin Zakaria (73484)------------------->
<!---3. Iman Tarmizi Rosalina (73496)----------------------->
<!---4. Faizatul Fitri Bin Boestamam (75351)---------------->

<!DOCTYPE html>
<html>
<head>
<title>Trees List</title>
<style>


h1 {
	text-align: center;
	background-color: #99CB0E;
	color: white;
	border-radius: 6px;
	font-family: 'Trebuchet MS', sans-serif;
}


.TableReport {
	background-color: #90EE90;
	border-radius: 6px;
	padding: 10px;
	margin: 7px 32px 21px 32px;
	border-radius: 15px;
	box-shadow: 0 10px 35px;
}

table {
	font-family: Arial;
	border-collapse: collapse;
	box-shadow: 0 10px 35px;
	margin: 8px 8px 40px 8px;
	padding: 40px;
	width: 98%;
}

td, th {
	border: 6px solid #f2f2f2;
	padding: 6px;
}
tr
{
	background-color: #F5F5F5;
	color: #000000;
}

th {
	text-align: left;
	background-color: #32CD32;
	color: white;
	padding-top: 12px;
	padding-bottom: 12px;
}

button {
  background-color: #32CD32;
  border-radius: 8px;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}
body 
		{ 
			background-color: #075E54;
		}
</style>
</head>

<body>

<h1>Trees Based on Planting Dates</h1>

<a href="viewReport.php">  
    <button>Return Previous Page</button>  
  </a>
<div class="TableReport">
<table>
	<tr>
		<th>Update ID</th>
		<th>Staff ID (Manager)</th>
		<th>Tree ID</th>
		<th>Tree Image</th>
		<th>Tree Height (m)</th>
		<th>Tree Diameter (m)</th>
		<th>Tree Status (Sold)</th>
		<th>Update Date</th>
	</tr>

<?php

include(__DIR__ . '/../dbConnect.php');

if(!$conn)
{
	die("connection failed: ".mysqli_connect_error());
}

$sql="SELECT * FROM treeupdate";
$result=mysqli_query($conn,$sql);
if(mysqli_num_rows($result)>0)
{
	while($row=mysqli_fetch_assoc($result)){
		$id=$row['UpdateID'];
		?>
		<tr>
			<td><?php echo $row['UpdateID']; ?></td>
			<td><?php echo $row['StaffID']; ?></td>
			<td><?php echo $row['TreeID']; ?></td>
			<td><?php echo '<img src="data:image;base64,'.base64_encode($row['TreeImage']).'" alt="Image">'; ?></td>
			<td><?php echo $row["TreeHeight"]; ?></td>
			<td><?php echo $row["TreeDiameter"]; ?></td>
			<td><?php echo $row["TreeStatus"]; ?></td>
			<td><?php echo $row["UpdateDate"]; ?></td>
		</tr>
		<?php
	}
}else{
	echo "no results";
}
mysqli_close($conn);

?>
</table>
</div>

<a href="viewReport.php">  
    <button>Return Previous Page</button>  
  </a>
</body>

</html>