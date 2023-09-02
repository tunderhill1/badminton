<?php 

function displayMatches($limit,$when){
	global $database, $session;
	// $when = 1, display future upcoming sessions, otherwise all sessions incl. past (for admin pages)
	if($when==1){
		// matches are removed after they start
		$q = "SELECT match_id,match_start,opponent,location,places,match_details FROM ".TBL_MATCH_LIST." WHERE match_start > NOW() ORDER BY match_start LIMIT $limit";
	}else{
		$q = "SELECT match_id,match_start,opponent,location,places,match_details FROM ".TBL_MATCH_LIST." ORDER BY match_start LIMIT $limit";
	}
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming matches. Please check back later.</p>";
		return;
	}

	if($session->userlevel >= LEVEL_GUEST){ ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Opponent</th>
          <th>Location</th>
          <th>Captains</th>
          <th>Players</th>
          <th>Queue</th>
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
          <th>Opponent</th>
          <th>Location</th>
          <th>Captains</th>
          <th>Players</th>
          <th>Queue</th>
          <th class="additional-info">Additional Info</th>
        </tr>
      </thead>
<?php   }

  /* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$match_id  = mysql_result($result,$i,"match_id");
		$match_start  = mysql_result($result,$i,"match_start");
		$opponent = mysql_result($result,$i,"opponent");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$match_details  = mysql_result($result,$i,"match_details");
		if(empty($match_details)){
			$match_details="N/A";
		}

		$date = date("D jS M Y", strtotime($match_start));
		$start_time = date("G:i", strtotime($match_start));

		$locationlink = $location; //No links for matches

		$places_playing = $database->getNumMatchPlayers($match_id);
		$places_left = $places - $places_playing;
		$places_queue = $database->getNumMatchQueue($match_id);
		
		// if member, or IP==Imperial's IP, show View & session managers
		if($session->userlevel >= LEVEL_GUEST){
			$opbuttons = "<a href=\"" .SITE_URL. "/matches/view/index.php?mid=$match_id\">View</a>";
			$match_captains = displayMatchCaptains($match_id);
		}else{
			$match_captains = $database->getNumCaptains($match_id);
		}

		// if activated member, show Queue/Remove/Manage options
		if($session->userlevel >= LEVEL_MEMBER){
			// if not booked, and spaces available, show Add
			$username = $session->username;
			$status = $database->getUserMatchStatus($username, $match_id);
			
			if($status=="Queue"){
				//if queued, show remove from queue
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
					<input type=\"hidden\" name=\"matchid\" value=\"$match_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"removequeue\" value=\"Unqueue\" class=\"link\"></form>";
			} elseif($status=="Player") {
				//if playing, show remove from roster
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
					<input type=\"hidden\" name=\"matchid\" value=\"$match_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"removeplaying\" value=\"Remove from Team\" class=\"link\"></form>";

			} elseif($status=="Captain") {
				//if captain, show remove captain
				$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
					<input type=\"hidden\" name=\"matchid\" value=\"$match_id\">";
				$opbuttons .= "<input type=\"submit\" name=\"removecaptain\" value=\"Remove Captain\" class=\"link\"></form>";

			} else {
				//if not queued or captain, show add
				if($session->userlevel >= LEVEL_COMMITTEE){
					$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
						<input type=\"hidden\" name=\"matchid\" value=\"$match_id\">";
					$opbuttons .= "<input type=\"submit\" name=\"addcaptain\" value=\"Add Captain\" class=\"link\"></form>";
				} 

				if($session->userlevel >= LEVEL_MEMBER) {
					$opbuttons .= "<form action=\"".SITE_URL."/include/process.php\" method=\"POST\">
						<input type=\"hidden\" name=\"matchid\" value=\"$match_id\">";
					$opbuttons .= "<input type=\"submit\" name=\"addqueue\" value=\"Queue\" class=\"link\"></form>";	
				}
			}
		}
		echo "<tr><td>$date</td><td>$start_time</td><td>$opponent</td><td>$locationlink</td><td>$match_captains</td><td>$places_playing/$places</td><td>$places_queue</td><td>$match_details</td>";
		if($session->userlevel >= LEVEL_GUEST){ echo "<td>$opbuttons</td>"; }
		echo "</tr>";
	}
	echo "<tfoot><tr></tr></tfoot></table>";
  echo "</div>";
}

function displayGivenMatchDetails($match_id){
	global $database, $session;

	if((string)$match_id === (string)(int)$match_id){/**/}else{ echo "<p>This session does not exist.</p>"; return; }

	$q = "SELECT match_start,opponent,location,places,match_details FROM ".TBL_MATCH_LIST." WHERE match_id = '$match_id' LIMIT 1";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || $num_rows>1){ echo "<p>Error displaying info</p>"; return; }
	if($num_rows==0){ echo "<p>This session does not exist.</p>"; return; }

	$match_start  = mysql_result($result,0,"match_start");
	$opponent = mysql_result($result,0,"opponent");
	$location  = mysql_result($result,0,"location");
	$places  = mysql_result($result,0,"places");
	$match_details  = mysql_result($result,0,"match_details");
	if(empty($match_details)){
		$match_details="N/A";
	}

	$date = date("D jS M Y", strtotime($match_start));
	$start_time = date("G:i", strtotime($match_start));

	$locationlink = $location; //no link for matches

	$places_playing = $database->getNumMatchPlayers($match_id);
	$places_left = $places - $places_playing;
	$places_queue = $database->getNumMatchQueue($match_id);

	$match_captains = displayMatchCaptains($match_id);

	echo "<div class=\"table-wrapper\"><table><tr><th>Date</th><td>$date</td></tr><tr><th>Time</th><td>$start_time</td></tr><tr><th>Opponent</th><td>$opponent</td></tr><tr><th>Location</th><td>$locationlink</td></tr><tr><th>Captains</th><td>$match_captains</td></tr><tr><th>Players</th><td>$places_playing/$places</td></tr><tr><th>Queue</th><td>$places_queue</td></tr></table></div>\n";
}

function displayMatchBookings($match_id){
   global $database, $session;
	// confusing err msg if non-integer session_id [i.e. a "hacker"]
	if((string)$match_id === (string)(int)$match_id){/**/}else{ echo "<p>No users have booked onto the match.</p>"; return; }

	$q2 = "SELECT places FROM ".TBL_MATCH_LIST." WHERE match_id = '$match_id' LIMIT 1";
	$r2 = $database->query($q2);
	$num_rows = mysql_numrows($r2);
	if($num_rows==1){ $limit  = mysql_result($r2,0,"places"); }else{ $limit=50; }

	$q_p = "SELECT username FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND status = 'Player' ORDER BY booking_date LIMIT $limit";
	$result_p = $database->query($q_p);
	/* Error occurred, return given name by default */
	$num_rows_p = mysql_numrows($result_p);

	$q_q = "SELECT username FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND status = 'Queue' ORDER BY booking_date LIMIT $limit";
	$result_q = $database->query($q_q);
	/* Error occurred, return given name by default */
	$num_rows_q = mysql_numrows($result_q);

	if(!$result_q || ($num_rows_q < 0) || !$result_p || ($num_rows_p < 0)){ echo "Error displaying info"; return; }

	// if($session->userlevel>=LEVEL_MANAGER){
	// 	echo "<form action=\"no_show.php\" method=\"post\" id=\"noshow\">
	// 		<div class=\"table-wrapper\"><table><thead><tr><th></th><th> User </th><th> No-show </th></tr></thead>";
	// }else{
	// 	echo "<div class=\"table-wrapper\"><table><thead><tr><th></th><th>User</th></tr></thead>";
	// }
	if($num_rows_p == 0){
		echo "<p>No users are playing in this match.</p>";  
	} else {
		if($session->userlevel>=LEVEL_COMMITTEE){
			echo "<form action=\"remove_player.php\" method=\"post\" id=\"removeplayer\">
				<div class=\"table-wrapper\"><table><thead><tr><th>Players</th><th>User</th><th>Remove Player</th></tr></thead>";
		}else{
			echo "<div class=\"table-wrapper\"><table><thead><tr><th>Players</th><th>User</th></tr></thead>";
		}

		$id = 0;
		for($i=0; $i<$num_rows_p; $i++){
			$un  = mysql_result($result_p,$i,"username");
			$id++;

			$name_array = $database->getNamefromUsername($un);
			$fullname = $name_array['firstname']." ".$name_array['lastname'];
			if($name_array['nickname']!=""){
				$fullname = $fullname." (".$name_array['nickname'].")";
			}

			if($session->userlevel>=LEVEL_COMMITTEE){
				echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td><td><input type=\"checkbox\" name=\"removeplayer[]\" value=$un></form></td></tr>";
			}elseif($session->userlevel>=LEVEL_MEMBER || $session->logged_in){
				echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td></tr>";
			}else{
				echo "<tr><td>$id</td><td>$fullname</td></tr>";
			}
		}
		if ($session->userlevel>=LEVEL_COMMITTEE){
			echo "<tfoot><tr></tr></tfoot></table></div>";
			if($num_rows_p > 0){
				echo "<input type=\"hidden\" name=\"match_id\" value=$match_id><input form=\"removeplayer\" type=\"submit\" name=\"Submit\" value=\"Remove Players\">";
			}
			echo "</form>";
		}else{
			echo "<tfoot><tr></tr></tfoot></table></div>";
		}
	}

	if($num_rows_q == 0){
		echo "<p>No users are queued for this match.</p>";
	} else {
		//Display Queue
		if($session->userlevel>=LEVEL_COMMITTEE){
			echo "<form action=\"add_player.php\" method=\"post\" id=\"addplayer\">
				<div class=\"table-wrapper\"><table><thead><tr><th>Queue</th><th>User</th><th>Add Player</th></tr></thead>";
		}else{
			echo "<div class=\"table-wrapper\"><table><thead><tr><th>Queue</th><th>User</th></tr></thead>";
		}

		$id = 0;
		for($i=0; $i<$num_rows_q; $i++){
			$un  = mysql_result($result_q,$i,"username");
			$id++;

			$name_array = $database->getNamefromUsername($un);
			$fullname = $name_array['firstname']." ".$name_array['lastname'];
			if($name_array['nickname']!=""){
				$fullname = $fullname." (".$name_array['nickname'].")";
			}

			if($session->userlevel>=LEVEL_COMMITTEE){
				echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td><td><input type=\"checkbox\" name=\"addplayer[]\" value=$un></form></td></tr>";
			}elseif($session->userlevel>=LEVEL_MEMBER || $session->logged_in){
				echo "<tr><td>$id</td><td><a href=\"".SITE_URL."/user/info/index.php?user=$un\">$fullname</a></td></tr>";
			}else{
				echo "<tr><td>$id</td><td>$fullname</td></tr>";
			}
		}
		if ($session->userlevel>=LEVEL_COMMITTEE){
			echo "<tfoot><tr></tr></tfoot></table></div>";
			if($num_rows_q > 0){
				echo "<input type=\"hidden\" name=\"match_id\" value=$match_id><input form=\"addplayer\" type=\"submit\" name=\"Submit\" value=\"Add Players\">";
			}
			echo "</form>";
		}else{
			echo "<tfoot><tr></tr></tfoot></table></div>";
		}
	}
	return;
}

function displayMatchCaptains($match_id){
	global $database, $session;
	// $session_id (int) type-tested in parent function
	$q = "SELECT username FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted='0' AND status = 'Captain'";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ $match_captains = "Error displaying info."; return $match_captains; }
	if($num_rows == 0){	$match_captains = "Currently no captains"; return $match_captains; }
	$match_captains = "<ul class=\"inline\">";
	for($i=0; $i<$num_rows; $i++){
		$uname  = mysql_result($result,$i,"username");

		$name_array = $database->getNamefromUsername($uname);
		$fullname = $name_array['firstname']." ".$name_array['lastname'];
		if($name_array['nickname']!=""){
			$fullname = $fullname." (".$name_array['nickname'].")";
		}

		if($session->userlevel>=LEVEL_MEMBER || $session->logged_in){
			$match_captains .= "<li><a href=\"".SITE_URL."/user/info/index.php?user=$uname\">$fullname</a></li>";
		}else{
			$match_captains .= "<li>$fullname</li>";
		}
	}
	$match_captains .= "</ul>";
	return $match_captains;
}
?>
