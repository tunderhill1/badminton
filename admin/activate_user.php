<?php  
include("../include/session.php");

function displayUnactivatedUsers(){
	global $database;
	$q = "SELECT * FROM ".TBL_USERS." WHERE active=0 ORDER BY firstname";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "Error displaying info";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are no un-activated users.</p>";
		return;
	}
	/* Display table contents */
	echo "<table border=\"1\">";
	echo "<tr> <th>Username</th> <th>Name</th> <th>Nickname</th> <th>Userlevel</th> <th>Allow active?</th> <th>Alt. email</th> <th>ACTIVATE</th> </tr>";
	for($i=0; $i<$num_rows; $i++){
		$username	= mysql_result($result,$i,"usr_name");
		$firstname	= mysql_result($result,$i,"firstname");
		$lastname	= mysql_result($result,$i,"lastname");
		$lvl		= mysql_result($result,$i,"usr_lvl");
		$allow		= mysql_result($result,$i,"allow_active");
		$nick		= mysql_result($result,$i,"nickname");
		$alt_email	= mysql_result($result,$i,"alt_email");

		if($allow == 1){ $allowed = "Y"; }elseif($allow == 0){ $allowed = "Nope"; }

		echo "<form action=\"adminprocess.php\" method=\"POST\">";
		echo "<tr> <td>$username</td> <td>$firstname $lastname</td> <td>$nick</td> <td>$lvl</td> <td>$allowed</td> <td>$alt_email</td>";
		echo "<td><input type=\"hidden\" name=\"activate\" value=\"1\" /><input type=\"submit\" value=\"Activate\" /><input type=\"hidden\" name=\"actuser\" value=\"$username\" /><input type=\"hidden\" name=\"subactuser\" value=\"1\" /></form></td>";
		echo "</tr>";
	}
	echo "</table><br/>";
}

if($session->userlevel >= LEVEL_ADMIN){
	?>
	<html>
	<title>ICBC Website Administration | Activate user </title>
	<body>

	<h1>Activated users</h1>

	<p><a href="<?php   echo SITE_URL ?>/admin/">Back to main admin page</a></p>
	<p><a href="../">Back to ICBC website</a></p>
	<?php  

	if(isset($_SESSION['actusersuccess'])){
		if($_SESSION['actusersuccess']){
			echo "<p>Successful activation!</p>";
		}else{
			echo "<p>User activation has <b>failed</b>.</p>";
		}
		unset($_SESSION['actusersuccess']);
	}

	displayUnactivatedUsers();
?>
	</body>
	</html>
<?php  
}else{ header("Location: " . SITE_URL); }
?>
