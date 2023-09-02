<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script>
	function check_uncheck_checkbox(isChecked) {
		if(isChecked) {
			$('input[name="noshow[]"]').each(function() { 
				this.checked = true; 
			});
		} else {
			$('input[name="noshow[]"]').each(function() {
				this.checked = false;
			});
		}
	}
</script>

<?php 

function locationLinks($location){
	// location links
	if($location == "Ethos"){
		$link = "https://goo.gl/maps/E7avQqxJYmJ2";
		$locationlink = "<a href=\"$link\" target=\"_blank\">$location</a>";
		return $locationlink;
	}
	elseif($location == "Kensington Leisure Centre"){
		$link = "https://goo.gl/maps/bPWJaVUQND12";
		$locationlink = "<a href=\"$link\" target=\"_blank\">$location</a>";
		return $locationlink;
	}
	elseif($location == "South Bank University"){
		$link = "https://goo.gl/maps/LUvkYQ2wzGo";
		$locationlink = "<a href=\"$link\" target=\"_blank\">$location</a>";
		return $locationlink;
	}
    elseif($location == "St John Bosco College"){
		$link = "https://goo.gl/maps/9EMCsWcwPUp";
		$locationlink = "<a href=\"$link\" target=\"_blank\">$location</a>";
		return $locationlink;
    }
	else{
		return $location;
	}
}

function displaySessions($limit,$when){
	global $database, $session;

	// $when = 1, display future upcoming sessions, otherwise all sessions incl. past (for admin pages)
	if($when==1){
		// sessions are removed 1 hour after they end
		$q = "SELECT session_id,session_start,session_end,location,places,training_details FROM ".TBL_SESSION_LIST." WHERE DATE_ADD(session_end, INTERVAL 1 HOUR) > NOW() ORDER BY session_start LIMIT $limit";
	}else{
		$q = "SELECT session_id,session_start,session_end,location,places,training_details FROM ".TBL_SESSION_LIST." ORDER BY session_start LIMIT $limit";
	}
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming sessions. Please check back later.</p>";
		return;
	}

	if($session->userlevel >= LEVEL_GUEST){ ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Location</th>
          <th>Managers</th>
          <th>Places left</th>
          <th class="additional-info">Additional Info</th>
          <th>Operations</th>
        </tr>
      </thead>
  <?php   }else{ ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Location</th>
          <th>Managers</th>
          <th>Places left</th>
          <th class="additional-info">Additional Info</th>
        </tr>
      </thead>
<?php   }

  /* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$session_id  = mysql_result($result,$i,"session_id");
		$session_start  = mysql_result($result,$i,"session_start");
		$session_end  = mysql_result($result,$i,"session_end");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$training_details  = mysql_result($result,$i,"training_details");
		if(empty($training_details)){
			$training_details="N/A";
		}

		$date = date("D jS M Y", strtotime($session_start));
		$start_time = date("G:i", strtotime($session_start));
		$end_time = date("G:i", strtotime($session_end));

		$locationlink = locationLinks($location);

		$places_booked = $database->getNumSessionBookings($session_id);
		$places_left = $places - $places_booked;

		// if member, or IP==Imperial's IP, show View & session managers
		if($session->userlevel >= LEVEL_GUEST){
			$opbuttons = "<a href=\"" .SITE_URL. "/sessions/view/index.php?sid=$session_id\">View</a>";
			$session_managers = displaySessionManagers($session_id);
		}else{
			$session_managers = $database->getNumManagers($session_id)."/".$database->getMaxNumManagers($session_id);
		}

		// if activated member, show Add/Remove/Manage options
		if($session->userlevel >= LEVEL_MEMBER){
			
			$dayofweek = date("w", strtotime($session_start));
			//if this is a coaching session and not booked to coaching session, and spaces available, show Add
			if($dayofweek == 0 && !$database->isUserAlreadyBookedTraining($session->username) && !$database->isUserBookedToSession($session->username,$session_id) && $database->remainingSessionPlaces($session_id)>0){
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"addbooking\" value=\"Book\" class=\"link\"></form>";
                if($places  == 18){
				    $opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				    <input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				    $opbuttons .= "<input type=\"submit\" name=\"paying\" value=\"Pay\" class=\"link\"></form>";
				}

			}else if ($dayofweek != 0 && !$database->isUserAlreadyBookedNonTraining($session->username) && !$database->isUserBookedToSession($session->username,$session_id) && $database->remainingSessionPlaces($session_id)>0){
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"addbooking\" value=\"Book\" class=\"link\"></form>";
				if($places  == 18){
				    $opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				    <input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				    $opbuttons .= "<input type=\"submit\" name=\"paying\" value=\"Pay\" class=\"link\"></form>";
				}

			}

			// if not booked to non-training, and spaces available, show Add
			// if(!$database->isUserAlreadyBookedNonTraining($session->username) &&
			// 	!$database->isUserBookedToSession($session->username,$session_id) &&
			// 	$database->remainingSessionPlaces($session_id)>0){
			// 	$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
			// 	<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
			// 	$opbuttons .= "<input type=\"submit\" name=\"addbooking\" value=\"Book\" class=\"link\"></form>";
			// }

			// if not booked, and spaces available, show Add
			// if(!$database->isUserAlreadyBooked($session->username, date("w", strtotime($session_start)), date("W", strtotime($session_start))) &&
			// 	!$database->isUserBookedToSession($session->username,$session_id) &&
			// 	$database->remainingSessionPlaces($session_id)>0){
			// 	$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
			// 	<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
			// 	$opbuttons .= "<input type=\"submit\" name=\"addbooking\" value=\"Book\" class=\"link\"></form>";
			// }

			// if booked to this session, show Remove
			if($database->isUserBookedToSession($session->username,$session_id)){
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"removebooking\" value=\"Remove\" class=\"link\"></form>";
			}

			// if committee, managing less than 2, not managing this, and spaces available, show Manage
			if($session->userlevel>=LEVEL_MANAGER && !$database->isUserAtManageLimit($session->username) && !$database->isUserBookedToManage($session->username,$session_id) && $database->remainingSessionManagers($session_id)>0){
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"addmanager\" value=\"Manage\" class=\"link\"></form>";
			}

			// if managing this session, show Un-manage
			if($database->isUserBookedToManage($session->username,$session_id)){
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
				<input type=\"hidden\" name=\"sessionid\" value=\"$session_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"removemanage\" value=\"Unmanage\" class=\"link\"></form>";
			}
		}
		echo "<tr><td>$date</td><td>$start_time - $end_time</td><td>$locationlink</td><td>$session_managers</td><td>$places_left/$places</td><td>$training_details</td>";
		if($session->userlevel >= LEVEL_GUEST){ echo "<td>$opbuttons</td>"; }
		echo "</tr>";
	}
	echo "<tfoot><tr></tr></tfoot></table>";
  echo "</div>";
}

function displayGivenSessionDetails($session_id){
	global $database, $session;

	if((string)$session_id === (string)(int)$session_id){/**/}else{ echo "<p>This session does not exist.</p>"; return; }

	$q = "SELECT session_start,session_end,location,places,training_details FROM ".TBL_SESSION_LIST." WHERE session_id = '$session_id' LIMIT 1";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || $num_rows>1){ echo "<p>Error displaying info</p>"; return; }
	if($num_rows==0){ echo "<p>This session does not exist.</p>"; return; }

	$session_start  = mysql_result($result,0,"session_start");
	$session_end  = mysql_result($result,0,"session_end");
	$location  = mysql_result($result,0,"location");
	$places  = mysql_result($result,0,"places");
	$training_details  = mysql_result($result,0,"training_details");
	if( empty($training_details)){
		$training_details="N/A";
	}

	$date = date("D jS M Y", strtotime($session_start));
	$start_time = date("G:i", strtotime($session_start));
	$end_time = date("G:i", strtotime($session_end));

	$locationlink = locationLinks($location);

	$places_booked = $database->getNumSessionBookings($session_id);
	$places_left = $places - $places_booked;

	$session_managers = displaySessionManagers($session_id);

	echo "<div class=\"table-wrapper\"><table><tr><th>Date</th><td>$date</td></tr><tr><th>Time</th><td>$start_time - $end_time</td></tr><tr><th>Location</th><td>$locationlink</td></tr><tr><th>Managers</th><td>$session_managers</td></tr><tr><th>Places left</th><td>$places_left / $places</td></tr></table></div>\n";
}

function displaySessionBookings($session_id){
   global $database, $session;
	// confusing err msg if non-integer session_id [i.e. a "hacker"]
	if((string)$session_id === (string)(int)$session_id){/**/}else{ echo "<p>No users have booked onto the session.</p>"; return; }

	$q2 = "SELECT places FROM ".TBL_SESSION_LIST." WHERE session_id = '$session_id' LIMIT 1";
	$r2 = $database->query($q2);
	$num_rows = mysql_numrows($r2);
	if($num_rows==1){ $limit  = mysql_result($r2,0,"places"); }else{ $limit=50; }

	$q = "SELECT username FROM ".TBL_SESSION_BOOKING." WHERE session_id = '$session_id' AND deleted = '0' ORDER BY booking_date LIMIT $limit";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ echo "Error displaying info"; return; }
	if($num_rows == 0){ echo "<p>No users have booked onto the session.</p>"; return; }

	if($session->userlevel>=LEVEL_MANAGER){
		// echo "<form action=\"".SITE_URL."/include/process.php\" method=\"post\" id=\"noshow\">
		echo "<form action=\"no_show.php\" method=\"post\" id=\"noshow\">
			<div class=\"table-wrapper\"><table><thead><tr><th></th><th> User </th><th> No-show </th></tr></thead>";
		echo "<tr><td></td><td><b>Select/Unselect All</b></td><td> <input type=\"checkbox\" name=\"checkall\" id=\"checkall\" onClick=\"check_uncheck_checkbox(this.checked);\"> </td></tr>";

	}else{
		echo "<div class=\"table-wrapper\"><table><thead><tr><th></th><th>User</th></tr></thead>";
	}

	$id = 0;
	for($i=0; $i<$num_rows; $i++){
		$un  = mysql_result($result,$i,"username");
		$id++;

		$name_array = $database->getNamefromUsername($un);
		$fullname = $name_array['firstname']." ".$name_array['lastname'];
		if($name_array['nickname']!=""){
			$fullname = $fullname." (".$name_array['nickname'].")";
		}

		if($session->userlevel>=LEVEL_MANAGER){
			echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td><td><input type=\"checkbox\" name=\"noshow[]\" value=$un></form></td></tr>";
		}elseif($session->userlevel>=LEVEL_MEMBER || $session->logged_in){
			echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td></tr>";
		}else{
			echo "<tr><td>$id</td><td>$fullname</td></tr>";
		}
	}
	if ($session->userlevel>=LEVEL_MANAGER){
		echo "<tfoot><tr></tr></tfoot></table></div></form>
			<input type=\"hidden\" name=\"noshow_session_id\" value=$session_id><input form=\"noshow\" type=\"submit\" name=\"Submit\" value=\"Submit Noshows\">";
	}else{
		echo "<tfoot><tr></tr></tfoot></table></div>";
	}
}

function displaySessionManagers($session_id){
	global $database, $session;
	// $session_id (int) type-tested in parent function
	$q = "SELECT username FROM ".TBL_SESSION_MANAGER." WHERE session_id = '$session_id' AND deleted='0'";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ $session_managers = "Error displaying info."; return $session_managers; }
	if($num_rows == 0){	$session_managers = "Currently no managers"; return $session_managers; }
	$session_managers = "<ul class=\"inline\">";
	for($i=0; $i<$num_rows; $i++){
		$uname  = mysql_result($result,$i,"username");

		$name_array = $database->getNamefromUsername($uname);
		$fullname = $name_array['firstname']." ".$name_array['lastname'];
		if($name_array['nickname']!=""){
			$fullname = $fullname." (".$name_array['nickname'].")";
		}

		if($session->userlevel>=LEVEL_MEMBER || $session->logged_in){
			$session_managers .= "<li><a href=\"".SITE_URL."/user/info/index.php?user=$uname\">$fullname</a></li>";
		}else{
			$session_managers .= "<li>$fullname</li>";
		}
	}
	$session_managers .= "</ul>";
	return $session_managers;
}
?>
