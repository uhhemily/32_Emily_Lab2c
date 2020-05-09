<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Police Emergency Service System</title>
<link href="pess.css" rel="stylesheet" type="text/css">
<?php
if (isset($_POST["btnUpdate"])) {
	
	require_once 'db_config.php';
	
	// create database connection
    $mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
    // Check connection
	if ($mysqli->connect_errno) {
		die("Failed to connect to MySQL: ".$mysqli->connect_errno);
	}
	
	// update patrol car status
	$sql = "UPDATE patrolcar SET patrolcarStatusId = ? WHERE patrolcarId = ?";
	
	if (!($stmt = $mysqli->prepare($sql))) {
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('ss', $_POST['patrolcarStatus'], $_POST['patrolcarId'])){
		die("Binding parameters failed: ".$stmt->errno);
	}
	
	if (!$stmt->execute()){
		die("Update patrolcar table failed: ".$stmt->errno);
	}
	
	// if patrol car status is Arrived (4) then capture the time of arrival
	if ($_POST["patrolcarStatus"] == '4'){
		
		$sql = "UPDATE dispatch SET timeArrived = NOW() WHERE timeArrived is NULL AND patrolcarId = ?";
		
		if (!($stmt = $mysqli->prepare($sql))) {
			die("Preapre failed: ".$mysqli->errno);
		}
		
		if (!$stmt->bind_param('s', $_POST['patrolcarId'])) {
			die("Binding parameters failed: ".$stmt->errno);
		}
		
		if (!$stmt->execute()) {
			die("Update dispatch table failed: ".$stmt->errno);
		}
		
	} 
	else if ($_POST["patrolcarStatus"] == '3'){ // else if patrol car status is FREE (3) then capture the time of completion
		
	// First, retrieve the incident ID from dispatch table handled by that patrol car
	$sql = "SELECT incidentId FROM dispatch WHERE timeCompleted IS NULL AND patrolcarId = ?";
		
	if (!($stmt = $mysqli->prepare($sql))) {
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('s', $_POST['patrolcarId'])){
		die("Binding parameters failed: ".$stmt->errno);
	}
		
	if (!$stmt->execute()) {
		die("Execute failed failed: ".$stmt->errno);
	}
		
	if (!($resultset = $stmt->get_result())) {
		die("Getting result set failed: ".$stmt->errno);
	}
	
	$incidentId;
		
	while ($row = $resultset->fetch_assoc()) {
		$incidentId = $row['incidentId'];//here
	}
		
	// next update dispatch table
	$sql = "UPDATE dispatch SET timeCompleted = NOW() WHERE timeCompleted is NULL AND patrolcarId = ?";
	
	if (!($stmt = $mysqli->prepare($sql))) {
		die("Prepare failed: ".$mysqli->errno);
	}
		
	if (!$stmt->bind_param('s', $_POST['patrolcarId'])) {
		die("Binding parameters failed: ".$stmt->errno);
	}
		
	if (!$stmt->execute()) {
		die("Update dispatch table failed: ".$stmt->errno);
	}
		
	// last but not least, update incident table to completed (3) all patrol car attended to it are FREE now
		
	$sql = "UPDATE incident SET incidentStatusId = '3' WHERE incidentId = '$incidentId' AND NOT EXISTS (SELECT * FROM dispatch WHERE timeCompleted IS NULL AND incidentId = '$incidentId')";
		
	if (!($stmt = $mysqli->prepare($sql))) {
		die("Preapare failed 11: ".$mysqli->errno);
	}
		
	$resultset->close();
	
}
	
    $stmt->close();
	
	$mysqli->close();

	?>
	
<script>window.location="logcall.php";</script>

<?php } ?>
</head>

<body>
<?php require_once 'nav.php'; ?>
<br><br>
<?php
if (!isset($_POST["btnSearch"])) {
?>
<!-- create a form to search for patrol car base on id --> 
<form name="form1" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table width="50%" border="0" align="center" cellpadding="4" cellspacing="4">
<tr></tr>
<tr>
<td>Patrol Car ID :</td>
<td><input type="text" name="patrolcarId" id="patrolcarId"></td>
<td><input type="submit" name="btnSearch" id="btnSearch" value="Search"></td>
</tr>
</table>		
</form>
<?php }

else 
{ // post back here after clicking the btnSearch button
require_once 'db_config.php';
	
// create database connection
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// Check connection 
if ($mysqli->connect_errno) {
   die("Failed to connect to MySQL: ".$mysqli->connect_errno);
}
	
// retrieve patrol car detail 
$sql = "SELECT * FROM patrolcar WHERE patrolcarId = ?";
	
if (!($stmt = $mysqli->prepare($sql))){
	die("Prepare failed: ".$mysqli->errno);
}
	
if (!$stmt->bind_param('s', $_POST['patrolcarId'])){
die("Binding parameters failed: ".$stmt->errno);
}
	
if (!$stmt->execute()){
	die("Execute failed failed: ".$stmt->errno);
}

if (!($resultset = $stmt->get_result())){
	die("Getting result set failed: ".$stmt->errno);
}
	
// if the patrol car does not exist, redirect back to update.php
if ($resultset->num_rows == 0) {
	?>
	<script>window.location="update.php";</script>
<?php }
	
// else if the patrol car found 
$patrolcarId;
$patrolcarStatusId;
	
while ($row = $resultset->fetch_assoc()){
	$patrolcarId = $row['patrolcarId'];
	$patrolcarStatusId = $row['patrolcarStatusId'];
}
	
// retrieve from patrolcar_status table for populating the combo box
$sql = "SELECT * FROM patrolcar_status";
if (!($stmt = $mysqli->prepare($sql))) {
	die("Prepare failed: ".$mysqli->errno);
}
	
if (!$stmt->execute()) {
	die("Execute failed: ".$stmt->errno);
}
	
if (!($resultset = $stmt->get_result())) {
	die("Getting result set failed: ".$stmt->errno);
} 
	
$patrolcarStatusArray; // an array variable
	
while ($row = $resultset->fetch_assoc()) {
	$patrolcarStatusArray[$row['statusId']] = $row['statusDesc'];
}
	
$stmt->close();
	
$resultset->close();
	
$mysqli->close();	
?>
	
<!-- display a form for operator to update status of patrol car -->
<form name="form2" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
<table width="50%" border="0" align="center" cellpadding="4" cellspacing="4">
<tr></tr>
<tr>
<td>ID :</td>
<td><?php echo $patrolcarId ?>
<input type="hidden" name="patrolcarId" id="patrolcarId" value="<?php echo $patrolcarId ?>">
</td>
</tr>
<tr>
<td>Status :</td>
<td><select name="patrolcarStatus" id="patrolcarStatus">
<?php foreach($patrolcarStatusArray as $key => $value){ ?>
<option value="<?php echo $key ?>"
<?php if ($key==$patrolcarStatusId) {?> selected="selected"
<?php }?>
>
	<?php echo $value ?>
	</option> 
<?php } ?>
</select></td>
</tr>
<tr>
<td><input type="reset" name="btnCancel" id="btnCancel" value="Reset"></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="btnUpdate" id="btnUpdate" value="Update"></td>
</tr>
</table>	
</form>
<?php } ?>
<div class="footer">
<p>Police Emergency Service System</p>
<p>Done by: Emily Chew Pei Yin/ 32</p>   
</div>
</body>
</html>