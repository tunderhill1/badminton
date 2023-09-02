<?php 
include("../include/session.php");

function displayActivatedEmails(){
	global $database;
	$q = "SELECT usr_name FROM ".TBL_USERS." WHERE active = 1";
	$result = $database->query($q);

	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ echo "Error"; return; }
	if($num_rows == 0){	echo "Database table empty."; return; }

	for($i=0; $i<$num_rows; $i++){
		$un	= mysql_result($result,$i,"usr_name");
		$email = $database->getEmailfromUsername($un);
		echo $email."<br>";
	}
}

if($session->userlevel >= LEVEL_ADMIN){ displayActivatedEmails(); }else{ header("Location: " . SITE_URL); }
?>
