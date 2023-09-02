<?php 
include("../../include/session.php");

$session_id = htmlentities(mysql_real_escape_string($_GET['sid']));

function displayGivenSessionDetails($session_id){
	global $database;

	$q = "SELECT * FROM ".TBL_SESSION_LIST." WHERE session_id='$session_id' LIMIT 1";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ echo "<p>Error displaying info</p>"; return; }
	if($num_rows == 0){ echo "<p>This session does not exist.</p>"; return; }

	$session_start  = mysql_result($result,0,"session_start");
	$session_end  = mysql_result($result,0,"session_end");
	$location  = mysql_result($result,0,"location");
	$places  = mysql_result($result,0,"places");

	$date = date("D jS M Y", strtotime($session_start));
	$start_time = date("G:i", strtotime($session_start));
	$end_time = date("G:i", strtotime($session_end));

	$places_booked = $database->getNumSessionBookings($session_id);
	$places_left = $places - $places_booked;

	$session_managers = displaySessionManagers($session_id);

	echo "<table>";
	echo "<tr><td>Date:</td><td><strong>$date</strong></td></tr>\n";
	echo "<tr><td>Time:</td><td><strong>$start_time - $end_time</strong></td></tr>\n";
	echo "<tr><td>Spare places:</td><td><strong>$places_left/$places</strong></td></tr>\n";
	echo "<tr><td>Managers:</td><td><strong>$session_managers</strong></td></tr>\n";
	echo "</table>";
}

function displaySessionBookings($session_id){
	global $database;

	$q2 = "SELECT places FROM ".TBL_SESSION_LIST." WHERE session_id='$session_id' LIMIT 1";
	$r2 = $database->query($q2);
	$num_rows = mysql_numrows($r2);
	if($num_rows==1){ $limit  = mysql_result($r2,0,"places"); }else{ $limit=50; }

	$q = "SELECT ".TBL_USERS.".usr_name, ".TBL_USERS.".firstname, ".TBL_USERS.".lastname, ".TBL_USERS.".nickname FROM ".TBL_USERS.", ".TBL_SESSION_BOOKING." WHERE ".TBL_SESSION_BOOKING.".session_id='$session_id' AND ".TBL_SESSION_BOOKING.".deleted='0' AND ".TBL_USERS.".usr_name = ".TBL_SESSION_BOOKING.".username ORDER BY booking_date LIMIT $limit";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "Error displaying info";
		return;
	}
	if($num_rows == 0){
		echo "<p>No users have booked onto the session.</p>";
		return;
	}

	echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"1\">";
	$id = 0;
	for($i=0; $i<$num_rows; $i++){
		$firstname = mysql_result($result,$i,"firstname");
		$lastname  = mysql_result($result,$i,"lastname");
		$nickname  = mysql_result($result,$i,"nickname");
		$fullname = $firstname." ".$lastname;

		if($nickname!=""){
			$fullname = $fullname." (".$nickname.")";
		}

		$id++;
		echo "<tr><td>&nbsp $id &nbsp</td><td align=\"center\">&nbsp $fullname &nbsp</td><td>&nbsp Yes / No &nbsp</tr>\n";
	}
	
	// Append session manager to list
	$q = "SELECT username FROM ".TBL_SESSION_MANAGER." WHERE session_id='$session_id' AND deleted=0";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		$session_managers = "Error appending manager info.";
		return;
	}

	for($i=0; $i<$num_rows; $i++){
		$username  = mysql_result($result,$i,"username");

		$name_array = $database->getNamefromUsername($username);
		$fullname = $name_array['firstname']." ".$name_array['lastname'];
		if($name_array['nickname']!=""){
			$fullname = $fullname." (".$name_array['nickname'].")";
		}

		$id++;
		echo "<tr><td>&nbsp $id &nbsp</td><td align=\"center\">&nbsp <b>$fullname</b> &nbsp</td><td>&nbsp Yes / No &nbsp</tr>\n";
	}
	echo "</table>";
}

function displaySessionManagers($session_id){
	global $database;

	$q = "SELECT username FROM ".TBL_SESSION_MANAGER." WHERE session_id='$session_id' AND deleted=0";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		$session_managers = "Error displaying info.";
		return $session_managers;
	}
	if($num_rows == 0){
		$session_managers = "Currently no managers.";
		return $session_managers;
	}

	$session_managers = "";

	for($i=0; $i<$num_rows; $i++){
		$username  = mysql_result($result,$i,"username");

		$name_array = $database->getNamefromUsername($username);
		$fullname = $name_array['firstname']." ".$name_array['lastname'];
		if($name_array['nickname']!=""){
			$fullname = $fullname." (".$name_array['nickname'].")";
		}

		if($i > 0){ $session_managers .= " and "; }

		$session_managers .= "$fullname";
	}

	return $session_managers;
}

if((string)$session_id === (string)(int)$session_id){
	if($session->userlevel>=LEVEL_GUEST){
		?>
		<html><title>ICBC Print Session</title><body>

		<?php 
		include("../../server_url.php");
		echo "<a href=\"".SITE_URL."/sessions\">Back to session list</a>";
		?>

		<?php 
		displayGivenSessionDetails($session_id);
		displaySessionBookings($session_id);
		?>
		</body></html>
		<?php 
	}else{ header("Location: " . SITE_URL . "/sessions/"); }	// no permission
}else{ header("Location: " . SITE_URL . "/sessions/"); }	// session_id not an integer
?>
