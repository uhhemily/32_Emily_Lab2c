<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Police Emergency Service System</title>
<link rel="stylesheet" type="text/css" href="pess.css">
</head>

<body>
<?php require 'nav.php';?>
<?php require 'db_config.php';
	
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
if ($stmt->execute())
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
<form name="frmLogcall" method="post" action="dispatch.php" onSubmit="return anymeaningfulname();">
<table width="40%" name="callerName" id="callername" cellpadding="4" cellspacing="4" align="center">
	
</table>
	
</form>
</fieldset>	
	
</body>
</html>
