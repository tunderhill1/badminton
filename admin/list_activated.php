<?php  
include("../include/session.php");

function displayActivatedUsersList(){
	global $database;
	$q = "SELECT * FROM ".TBL_USERS." WHERE active = 1 ORDER BY lastname";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>Database table empty.</p>";
		return;
	}
	/* Display table contents */
	echo "<table border=\"1\">";
	echo "<tr> <th>Username</th> <th>Name</th> <th>Nickname</th> <th>Userlevel</th> <th>Allow active?</th> <th>Alt. email</th> </tr>";
	for($i=0; $i<$num_rows; $i++){
		$username	= mysql_result($result,$i,"usr_name");
		$firstname	= mysql_result($result,$i,"firstname");
		$lastname	= mysql_result($result,$i,"lastname");
		$lvl		= mysql_result($result,$i,"usr_lvl");
		$allow		= mysql_result($result,$i,"allow_active");
		$nick		= mysql_result($result,$i,"nickname");
		$alt_email	= mysql_result($result,$i,"alt_email");

		if($allow == 1){ $allowed = "Y"; }elseif($allow == 0){ $allowed = "Nope"; }

		echo "<tr> <td>$username</td> <td>$firstname $lastname</td> <td>$nick</td> <td>$lvl</td> <td>$allowed</td> <td>$alt_email</td> </tr>";
	}
	echo "</table><br/>";
}

if($session->userlevel >= LEVEL_ADMIN){
?>
	<html>
	<title>ICBC Website Administration | Activated user list</title>
	<body>

	<h1>Activated user list</h1>

	<p><a href="<?php   echo SITE_URL ?>/admin/">Back to main admin page</a></p>
	<p><a href="../">Back to ICBC website</a></p>
<?php  
	displayActivatedUsersList();
?>
	</body>
	</html>
<?php  
}else{ header("Location: " . SITE_URL); }
?>
