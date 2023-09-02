<?php 

function becomeMember(){
    // **DEPRECATED
	global $database, $session;

	if($session->logged_in){
		if($session->activated==0){
			echo "<div id=\"introbox1\"><h3>Membership</h3>";
			echo "<p>You have <strong>not</strong> been activated</p><ul><li><a href=\"".SITE_URL."/about/activating/\">Getting activated</a></li></ul>";
			echo "</div>";
		}
	}else{
		echo "<div id=\"introbox1\"><h3>Membership</h3>";
		echo "<ul><li><a href=\"".SITE_URL."/about/membership/\">Becoming a member</a></li><li><a href=\"https://www.imperialcollegeunion.org/shop/club-society-project-products/badminton-products/6807/badminton-membership-14-15\" class=\"external\">Buy membership</a></li></ul>";
		echo "</div>";
	}
}

function displayNextSession(){
	global $database;

	echo "<div id=\"introbox1\"><h3>Next session</h3>";

	$q = "SELECT session_id, session_start,session_end,location,places FROM ".TBL_SESSION_LIST." WHERE session_start > NOW() ORDER BY session_start LIMIT 1";
	$result = mysql_query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){ echo "<p>Error displaying info</p>"; echo "</div>"; return; }
	if($num_rows == 0){	echo "<p>No session availability.</p><p>Please check back later.</p>"; echo "</div>"; return; }

	$session_id  = mysql_result($result,0,"session_id");
	$session_start  = mysql_result($result,0,"session_start");
	$session_end  = mysql_result($result,0,"session_end");
	$location  = mysql_result($result,0,"location");
	$places  = mysql_result($result,0,"places");
	$date = date("D jS M", strtotime($session_start));
	$start_time = date("G:i", strtotime($session_start));
	$end_time = date("G:i", strtotime($session_end));

	$places_booked = $database->getNumSessionBookings($session_id);
	$places_left = $places - $places_booked;

	echo "<p class=\"sessiondate\">$date</p><p class=\"sessiontimevenue\">$start_time to $end_time</p><p class=\"sessiontimevenue\">$location</p><p><b>$places_left</b>/$places places left, <a href=\"".SITE_URL."/sessions/view/index.php?sid=$session_id\">view</a>.</p></div>\n";
}

?>
