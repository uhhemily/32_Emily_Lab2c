<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" type="text/css" href="pess.css">
</head>

<body>
<script>
function fullname()
{
		var x=document.forms["frmLogcall"]["callerName"].value;
		if (x==null || x=="")
}
	{
		alert("Caller Name is required.");
		return false;
	}
	
function contactno()
{
	var y=document.forms["fromLogcall"]["contactNo"].value;
	if (y=="" || y==null || contactno.length < 8))	
}
{
    alert("Contact Number is required.");
	return false;
}
</script>
<?php require_once 'nav.php';?>
<?php require_once 'db_config.php';
	
$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	
if ($mysqli->connect_errno)
	{
		die("Failed to connect to MySQL: " .$mysqli->connect-errno);
	}
	

$sql = "SELECT * FROM incidenttype";
//Run sql command in $sql, if error, display error message and exit.
if (!($stmt = $mysqli->prepare($sql)))	
{
	die("SQL Command Error: ".$mysqli->errno);
}

//execute command
if (!$stmt->execute())
{
	die("Execution of SQL Command Failed: ".$stmt->errno);
}
	
//Check any data in resultset
if (!($resultset = $stmt->get_result()))
{
	die("No data is found in resultset: ".$stmt->errno);
}
	
$incidentType; //an array variable
	
while ($row = $resultset->fetch_assoc())
{
	//create an associative array of $incidentType 
	$incidentType[$row['incidentTypeId']] = $row['incidentTypeDesc'];
}
	
$stmt->close();
	
$resultset->close();
	
$mysqli->close();
	
?>
	
<fieldset>
<legend>Log Call</legend>
<form name="frmLogcall" method="post" action="dispatch.php" onSubmit="return fullname();">
<table width="40%" name="callerName" id="callername" cellpadding="4" cellspacing="4" align="center">

<tr>
<td width="50%">Caller's Name :</td>	
<td width="50%"><input type="text" name="callerName" id="callername" required></td>
</tr>	
<tr>
<td width="50%">Contact No. :</td>	
<td width="50%"><input type="text" name="contactNo" id="contactNo" required></td>	
</tr>
<tr>
<td width="50%">Location :</td>	
<td width="50%"><input type="text" name="location" id="location" required></td>	
</tr>
<tr>
<td width="50%">Incident Type :</td>	
<td width="50%"><select name="incidentType" id="incidentType" required>
	<?php foreach($incidentType as $key => $value) {?>
	<option value="<?php echo $key ?> " >
	<?php echo $value ?> </option>
	<?php } ?>						
</select>
</td>
</tr>
<tr>
<td width="50%">Description :</td>
<td width="50%"><textarea name="incidentDesc" id="incidentDesc" cols="45" rows="5" required></textarea></td>
</tr>
<tr>
<td><input type="reset" name="resetForm" id="resetForm" value="Reset"</td>
<td><input type="submit" name="submitForm" id="submitForm" value="Submit Call"</td>
</tr>
</table>	
</form>
</fieldset>	
    
<div class="footer">
<p>Police Emergency Service System</p>
<p>Done by: Emily Chew Pei Yin/ 32</p>   
</div>
</body>
</html>
